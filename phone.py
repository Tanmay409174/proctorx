from ultralytics import YOLO
import cv2

# Load your YOLO model (use the same one that worked before)
model = YOLO("yolov8n.pt")  # or yolov5s.pt if you were using v5

# Open webcam
cap = cv2.VideoCapture(0)
print("📱 Detecting phones... Press 'q' to quit.")

while True:
    ret, frame = cap.read()
    if not ret:
        break

    # Run YOLO inference (no console spam)
    results = model(frame, verbose=False)

    phone_detected = False

    # Iterate detections
    for box, cls in zip(results[0].boxes.xyxy, results[0].boxes.cls):
        label = model.names[int(cls)].lower()

        # Only detect "cell phone"
        if "cell phone" in label:
            phone_detected = True
            x1, y1, x2, y2 = map(int, box)
            cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 0, 255), 2)
            cv2.putText(frame, "Phone", (x1, y1 - 10),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.7, (0, 0, 255), 2)

    # Show webcam frame (no console prints)
    cv2.imshow("📱 Phone Detection", frame)

    # Quit on 'q'
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()
