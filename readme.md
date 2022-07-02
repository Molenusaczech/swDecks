# SW Decks
A system for submitting tournament decks

## How it works?
There are 3 ways to submit a deck:
- Manual input (Input fields support suggestions and checks for the legality of the deck)
- Import from the Scratch Wars app (using the official API)
- Import from the Scratch Wars Online website (scraping it, since it doesn't have an API)

## Encryption
The tournament and user data is is encrypted by a key, which is stored in the beginning of each php document

## Modularity
You can import custom tournament limits by putting the allowed card list in \heroes and \weapons and defining it in \limits.json