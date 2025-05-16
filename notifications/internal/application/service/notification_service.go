package service

import (
	"context"
	"telegram-backend/notifications/internal/domain"

	"github.com/go-kit/log"
)

type NotificationService interface {
	Send(ctx context.Context, message *domain.Message, chat *domain.Chat, member domain.Member) error
	FetchMessageByID(ctx context.Context, messageID int) (*domain.Message, error)
	FetchChatByID(ctx context.Context, chatID int) (*domain.Chat, error)
	FetchChatMembers(ctx context.Context, chatID int) ([]domain.Member, error)
}

type notificationService struct {
	logger log.Logger
}

func NewNotificationService(logger log.Logger) NotificationService {
	return &notificationService{
		logger: logger,
	}
}

func (s *notificationService) Send(ctx context.Context, message *domain.Message, chat *domain.Chat, member domain.Member) error {
	s.logger.Log(
		"msg", "Notification sent",
		"userID", member.ID,
		"chatName", chat.Name,
		"authorID", message.AuthorID,
		"message", message.Content,
	)
	return nil
}

func (s *notificationService) FetchMessageByID(ctx context.Context, messageID int) (*domain.Message, error) {
	return &domain.Message{
		ID:       messageID,
		AuthorID: 1,
		ChatID:   100,
		Content:  "Hello World",
	}, nil
}

func (s *notificationService) FetchChatByID(ctx context.Context, chatID int) (*domain.Chat, error) {
	return &domain.Chat{
		ID:   chatID,
		Name: "General",
	}, nil
}

func (s *notificationService) FetchChatMembers(ctx context.Context, chatID int) ([]domain.Member, error) {
	return []domain.Member{
		{ID: 1, Name: "Alice"},
		{ID: 2, Name: "Bob"},
		{ID: 3, Name: "Charlie"},
	}, nil
}
