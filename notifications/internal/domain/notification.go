package domain

type NotificationType string

const (
	NotificationTypeMessage NotificationType = "message"
	NotificationTypeCall    NotificationType = "call"
)

type Notification struct {
	Type      NotificationType `json:"type"`
	MessageID int              `json:"message_id"`
}
