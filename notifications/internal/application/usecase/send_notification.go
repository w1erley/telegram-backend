package usecase

import (
	"context"
	"encoding/json"
	"fmt"
	"telegram-backend/notifications/internal/application/service"
	"telegram-backend/notifications/internal/domain"

	"github.com/go-kit/log"
)

type SendNotificationUseCase interface {
	Execute(ctx context.Context, payload string) error
}

type sendNotificationUseCase struct {
	notificationService service.NotificationService
	logger              log.Logger
}

func NewSendNotificationUseCase(svc service.NotificationService, logger log.Logger) SendNotificationUseCase {
	return &sendNotificationUseCase{
		notificationService: svc,
		logger:              logger,
	}
}

func (uc *sendNotificationUseCase) Execute(ctx context.Context, payload string) error {
	var notif domain.Notification

	if err := json.Unmarshal([]byte(payload), &notif); err != nil {
		uc.logger.Log("msg", "Failed to unmarshal notification payload", "err", err)
		return fmt.Errorf("invalid notification payload: %w", err)
	}

	switch notif.Type {
	case domain.NotificationTypeMessage:
		return uc.handleMessageNotification(ctx, notif)
	case domain.NotificationTypeCall:
		return nil
	default:
		uc.logger.Log("msg", "Unknown notification type", "type", notif.Type)
		return fmt.Errorf("unknown notification type")
	}
}

func (uc *sendNotificationUseCase) handleMessageNotification(ctx context.Context, notif domain.Notification) error {
	message, err := uc.notificationService.FetchMessageByID(ctx, notif.MessageID)
	if err != nil {
		return err
	}

	chat, err := uc.notificationService.FetchChatByID(ctx, message.ChatID)
	if err != nil {
		return err
	}

	members, err := uc.notificationService.FetchChatMembers(ctx, chat.ID)
	if err != nil {
		return err
	}

	for _, member := range members {
		if member.ID == message.AuthorID {
			continue
		}
		err := uc.notificationService.Send(ctx, message, chat, member)
		if err != nil {
			uc.logger.Log("msg", "Failed to send notification", "userID", member.ID, "err", err)
		}
	}

	return nil
}
