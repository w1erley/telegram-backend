package redis

import (
	"context"
	"fmt"
	"telegram-backend/notifications/internal/application/usecase"

	"github.com/go-kit/log"
	"github.com/go-redis/redis/v8"
)

type RedisSubscriber struct {
	client  *redis.Client
	useCase usecase.SendNotificationUseCase
	logger  log.Logger
}

func NewRedisSubscriber(host, port string, uc usecase.SendNotificationUseCase, logger log.Logger) *RedisSubscriber {
	rdb := redis.NewClient(&redis.Options{
		Addr: fmt.Sprintf("%s:%s", host, port),
	})

	return &RedisSubscriber{
		client:  rdb,
		useCase: uc,
		logger:  logger,
	}
}

func (rs *RedisSubscriber) Subscribe(channel string) {
	pubsub := rs.client.Subscribe(context.Background(), channel)
	ch := pubsub.Channel()

	rs.logger.Log("msg", "Subscribed to channel", "channel", channel)

	for msg := range ch {
		err := rs.useCase.Execute(context.Background(), msg.Payload)
		if err != nil {
			rs.logger.Log("msg", "Error processing notification", "err", err)
		}
	}
}
