package domain

type Message struct {
	ID       int
	AuthorID int
	ChatID   int
	Content  string
}

type Chat struct {
	ID   int
	Name string
}

type Member struct {
	ID   int
	Name string
}
