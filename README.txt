Description:
AltHVZ is an alternative platform to HVZSource, aimed to be open source and easily modifiable for it's users. It provides all of the basic features of the HVZSource website, including player statistics tracking, player interaction, game statistics tracking, player group interaction, multi-game support, and player communications. In addition, AltHVZ features support for Arsenal functionality, native support for mission-relevant information, additional admin-side player handling system, player communications management system, and addition player group systems. Future developments to the platform are expected to bring additional features such as Bounty Board, direct Admin/Player communications, and control over feature availability to prevent feature creep.

Installation:
1) Run the SQL queries located in the Database-Side folder.
2) Deploy the contents of Server-Side folder in the corresponding folder of the server.
3) Register an account. At the moment, moderator privileges have to be provided by hand through Database editing (change `userteam` value in `hvzuserstate` with appropriate ID to -2, 1, or 4 for Dead, Undead, and Alive Moderator, respectively). Due to release of the project, changing this is a top priority.

Note(s):
 - Since this was originally developed for GoDaddy-deployed system, it does not use native PHP hashing functions, but a workaround by Anthony Ferrara. Due to release of the project, an alternative version using native PHP hashing may be developed.