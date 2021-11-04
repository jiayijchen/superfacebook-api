# Pseudocode

## Requirements
* Create a connection to local database using PHP
* Create database using given SQL file
* Create a minimum of four (CRUD) operations that affect the database
* Create function to display all data in database
* Display proper HTTP errors upon error

## Summary
### CRUD functions
**CREATE: _create_**  
Given a name, about me, and a biography (and an ability?), creates a new hero entry in the database.

**READ: _search_**  
Given a name, searches database for a hero with a matching name and returns that hero's information
- Name
- About Me
- Biography
- Abilities
- Relationships (Friends and Enemies)

**UPDATE: _update_**  
Given a name and parameters to update, such as About Me, Biography, Abilites?/Relationships? (stretch goals?), seaches database for hero with given name and updates heros information.

**DELETE: _delete_**  
Given a name, searches database for hero with given name and deletes database entry, abilities, and relationships.

### Others (stretch goals?)
**_empower_**
Given a name and an abilite, searches database for the given hero and empowers them with the given power (adds an ability to hero), creating a new ability type if it is not found.

**_befriend_**
Given two names, creates a friendly relationship between the two named heros. 

**_antagonize_**
Given two names, creates an unfriendly relationship between the two named heros.

## Functionality

**_create_**  
createHero()
- params: name, about_me, biography, (abilities? stretch goal)
- function: creates new entry to heroes table in database
- return: msg upon successful creation
- errors: duplicate entry, missing params

**_search_**  
searchHero()
- params: name
- function: selects hero with name from heroes table in database
- returns: information attach to that hero, abilities and relationships with hero id
- errors: hero not found

**_update_**  
updateHero()
- params: name, (atleast one) {about_me, biography, ability}
- function: selects hero with name from heroes table in database and updates given information
- returns: msg upon successful update
- errors: hero not found, missing params

**_delete_**  
deleteHero()
- params: name
- function: select hero with name from heroes table in database and deletes entry (and entries containing hero_id in other tables?)
- returns: msg upon successful delete
- errors: hero not found

