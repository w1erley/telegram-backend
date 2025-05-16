# Base image
FROM golang:1.20 as builder

WORKDIR /app

# Copy go.mod and go.sum
COPY go.mod go.sum ./

# Download dependencies
RUN go mod download

# Copy the source code
COPY . .

# Build the Go application with CGO disabled for compatibility with Alpine
RUN CGO_ENABLED=0 GOOS=linux GOARCH=amd64 go build -o notifications-service ./cmd/notifications

# Final stage (Using alpine for small image size)
FROM alpine:latest

WORKDIR /root/

# Install required dependencies for running Go binary
RUN apk --no-cache add ca-certificates

# Copy the binary from the builder
COPY --from=builder /app/notifications-service .

# Environment variables
#ENV REDIS_HOST=redis
#ENV REDIS_PORT=6379
#ENV JWT_SECRET=your_jwt_secret

# Expose the service port
EXPOSE 8082

# Run the application
CMD ["./notifications-service"]
