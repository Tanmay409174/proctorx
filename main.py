import cv2
import mediapipe as mp
import time
import requests
import sys

# Constants
TERMINATE_ENDPOINT = "http://127.0.0.1/examportal/terminate_exam.php"
CHECK_INTERVAL = 0.05
MIN_FACE_SIZE = 40
HORIZONTAL_OFFSET_THRESHOLD = 0.35
VERTICAL_OFFSET_THRESHOLD = 0.25

NO_FACE_PENALTY = 0.2
TURN_PENALTY = 0.2
RECOVERY_RATE = 0.3
VIOLATION_LIMIT = 13.0
RETRY_ATTEMPTS = 5
RETRY_DELAY = 2
POST_TERMINATION_WAIT = 5

STUDENT_NAME = ""  # Default student name
# --------------------------------

# MediaPipe setup
mp_face = mp.solutions.face_mesh
FACE_LANDMARKS = mp_face.FaceMesh


def call_terminate_endpoint(student_name=STUDENT_NAME):
    """Send a termination request to the server"""
    for attempt in range(1, RETRY_ATTEMPTS + 1):
        try:
            print(f"[monitor] Attempt {attempt}: sending termination request...")
            resp = requests.post(
                TERMINATE_ENDPOINT,
                data={'studentName': student_name},
                timeout=5
            )
            print(f"[monitor] Response {resp.status_code}: {resp.text.strip()[:150]}")
            if resp.status_code == 200:
                print("[monitor] ✅ Termination successfully logged on server.")
                return True
        except Exception as e:
            print(f"[monitor] ❌ Error contacting terminate_exam.php: {e}")
        time.sleep(RETRY_DELAY)
    print("[monitor] ❌ All termination attempts failed.")
    return False


def compute_nose_offset(landmarks, image_w, image_h, bbox):
    """Compute the offset of the nose to detect turned faces"""
    try:
        l1 = landmarks[1]
        l2 = landmarks[4]
    except Exception:
        center_index = len(landmarks) // 2
        l1 = l2 = landmarks[center_index]

    # Compute the nose offset
    nx = ((l1.x + l2.x) / 2.0) * image_w
    ny = ((l1.y + l2.y) / 2.0) * image_h
    x_min, y_min, x_max, y_max = bbox
    face_w = x_max - x_min
    face_h = y_max - y_min

    if face_w <= 0 or face_h <= 0:
        return 0.0, 0.0

    center_x = x_min + face_w / 2.0
    center_y = y_min + face_h / 2.0
    offset_x = (nx - center_x) / float(face_w)
    offset_y = (ny - center_y) / float(face_h)

    return offset_x, offset_y


def handle_violations(violation_accum):
    """Check for warning when violation count reaches 3 seconds"""
    if violation_accum >= 3.0 and violation_accum < 5.0:
        print("\n[monitor] 🚨 Warning: Violation count reached 3 seconds.")
        # Send a warning to the exam frontend immediately
        try:
            response = requests.post("http://127.0.0.1/examportal/warn_exam.php", data={'warning': '1'})
            if response.status_code == 200:
                print("[monitor] Warning sent to exam frontend immediately.")
            else:
                print("[monitor] ❌ Failed to send warning.")
        except requests.exceptions.RequestException as e:
            print(f"[monitor] ❌ Error sending warning: {e}")





def main():
    cap = cv2.VideoCapture(0)
    if not cap.isOpened():
        print("ERROR: Cannot open webcam.")
        sys.exit(1)

    fm = FACE_LANDMARKS(
        static_image_mode=False,
        max_num_faces=2,
        refine_landmarks=True,
        min_detection_confidence=0.5,
        min_tracking_confidence=0.5
    )

    violation_accum = 0.0
    print(f"[monitor] Monitoring started (terminate when viol_accum ≥ {VIOLATION_LIMIT}s).")

    try:
        while True:
            ret, frame = cap.read()
            if not ret:
                time.sleep(CHECK_INTERVAL)
                continue

            frame = cv2.flip(frame, 1)
            h, w, _ = frame.shape
            rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
            results = fm.process(rgb)

            face_present = False
            turned_away = False

            if results.multi_face_landmarks:
                if len(results.multi_face_landmarks) > 1:
                    print("\n[monitor] ❌ Multiple faces detected! Terminating exam...")
                    call_terminate_endpoint()
                    time.sleep(POST_TERMINATION_WAIT)
                    break

                mesh = results.multi_face_landmarks[0].landmark
                xs = [p.x for p in mesh]
                ys = [p.y for p in mesh]
                x_min = int(min(xs) * w)
                x_max = int(max(xs) * w)
                y_min = int(min(ys) * h)
                y_max = int(max(ys) * h)
                face_w = x_max - x_min
                face_h = y_max - y_min

                if face_w >= MIN_FACE_SIZE and face_h >= MIN_FACE_SIZE:
                    face_present = True
                    off_x, off_y = compute_nose_offset(mesh, w, h, (x_min, y_min, x_max, y_max))
                    if abs(off_x) > HORIZONTAL_OFFSET_THRESHOLD or abs(off_y) > VERTICAL_OFFSET_THRESHOLD:
                        turned_away = True

            # Penalties and recovery logic
            if not face_present:
                violation_accum += NO_FACE_PENALTY
            elif turned_away:
                violation_accum += TURN_PENALTY
            else:
                violation_accum -= RECOVERY_RATE
                if violation_accum < 0:
                    violation_accum = 0.0

            print(f"\rface_present={face_present} turned_away={turned_away} viol_accum={violation_accum:.2f}", end="")

            # Trigger termination if violation limit exceeded
            if violation_accum >= VIOLATION_LIMIT:
                print("\n[monitor] 🚨 Violation time exceeded 10s — triggering termination...")
                success = call_terminate_endpoint()
                if not success:
                    print("[monitor] ❗ Could not reach server — still stopping camera after wait.")
                time.sleep(POST_TERMINATION_WAIT)
                break

            # Check for warning when violation reaches 5
            handle_violations(violation_accum)

            time.sleep(CHECK_INTERVAL)

    except KeyboardInterrupt:
        print("\n[monitor] Interrupted manually by user.")
    finally:
        cap.release()
        fm.close()
        print("\n[monitor] Camera released, exiting cleanly.")


if __name__ == "__main__":
    main()
