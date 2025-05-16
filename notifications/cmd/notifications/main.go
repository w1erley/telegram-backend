package main

import (
	"os"
	"telegram-backend/notifications/internal/adapter/redis"
	"telegram-backend/notifications/internal/application/service"
	"telegram-backend/notifications/internal/application/usecase"
	"telegram-backend/notifications/internal/infrastructure/logger"
)

func main() {
	logg := logger.NewLogger()

	redisHost := os.Getenv("REDIS_HOST")
	redisPort := os.Getenv("REDIS_PORT")

	if redisHost == "" {
		redisHost = "redis"
	}

	if redisPort == "" {
		redisPort = "6379"
	}

	notifService := service.NewNotificationService(logg)

	sendNotifUseCase := usecase.NewSendNotificationUseCase(notifService, logg)

	subscriber := redis.NewRedisSubscriber(redisHost, redisPort, sendNotifUseCase, logg)

	channels := []string{"notifications:new_message"}

	for _, ch := range channels {
		go subscriber.Subscribe(ch)
	}

	logg.Log("msg", "Notification service started and listening for Redis events")

	select {}
}
