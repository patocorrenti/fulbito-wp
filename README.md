# Fulbito - Wordpress plugin (Beta)

Wordpress plugin for organizing and tracking the Futbol 5 you play with your friends.

## Features
- Schedule games and allow player subscription from the website
- Create teams manually, or automatically based on players averages
- Store the results from the game
- Punish the bad guys with suspensions
- Get the position table and the players profiles
- Have fun!

## Available languages
- Español
- English

## Installation
### Clone
- Clone this repository into your Wordpress install, inside the plugins folder wp-content/plugins/
- Activate the plugin from Wordpress administration
### ZIP file
- Download this repository as ZIP
- In your Wordpress go to administration -> plugins -> Add new
- Select Upload Plugin option
- Upload the ZIP file
- Activate the plugin

Fulbito works with any Wordpress theme.

## Getting started
Fulbito will create a new post type called "Games"
- Create players (you'll need at least 10)
- Create a game
  - Select 10 participants
  - Create 2 teams manually or with an automatic algorithm
- When the game is done you can load the result and the suspensions

## Shortcodes
- `[fulbito_tabla]` position table and players profile
- `[fulbito_inscripcion]` public subscription form
