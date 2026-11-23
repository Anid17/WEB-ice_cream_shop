# AI Log

## Milestone 1

### Tools and tasks
- **Claude (claude.ai chat):** guide for Milestone 1: Git and GitHub setup, ERD review, README draft and templates for AGENTS.md.
  Prompt: "I want you to help me do all of these for milestone1."
  I typed in and tested the code, chose the name Specijal and the orange and
  baby blue theme, added the Home page, redrew the ERD myself, and told it not to add the weather API in this milestone.
  I also asked it how to see and send my older commits (git log --oneline, git diff) so I could check what Copilot changed.
- **GitHub Copilot (completion):** I typed the comment
  "<!-- Bootstrap card with a title, short text and a button -->" and Copilot
  suggested a card. I accepted it only to test completions. I removed it afterwards, because it sat inside <main> and showed on every page.
- **GitHub Copilot (chat, GPT-5.6):** footer task from Lab 01.01.
  Prompt: "Add a footer to index.html with three columns: About, Links and
  Contact. Use Bootstrap 5 classes only, no custom CSS. Do not change anything else."
  I committed first ("before agent task") and then reviewed the result with
  git diff a6760d8 -- frontend/index.html. I kept the three-column footer.
- **GitHub Copilot (Backend agent), boundary test:**
  Prompt: "Add a footer to frontend/index.html".
  Result: it refused and told me to switch to the Frontend agent. This is the
  correct behavior.
- **GitHub Copilot (Reviewer agent):** Prompt: "Review frontend/views/login.html".
  It reported rule violations, bugs and security notes (see below). I decided
  myself which findings were real.

### Where the AI was wrong
1. (Claude) It gave me the path C:\xampp, but my XAMPP is in C:\xampp2. The cd
   command failed with "The system cannot find the path specified". I noticed it
   in the terminal output and used C:\xampp2 from then on.
2. (Claude) It gave me Windows cmd commands, but the VS Code terminal is
   PowerShell. mkdir failed with "A positional parameter cannot be found". I read
   the error, and the PowerShell versions worked.
3. (Claude) It told me to make a first commit on main, which was not needed
   because the repository already had the "Initial commit". I checked with
   git log --oneline and skipped that step.
4. (Copilot completion) The suggested card used the class "btn-primary", which
   is Bootstrap blue and does not match my orange theme. I noticed it in the
   browser. I kept it only as a test and did not use it as page content.
5. (Copilot chat) In the footer task it also commented out the welcome card in
   <main>, which I did not ask for ("Do not change anything else"). I noticed
   it in the git diff against my "before agent task" commit.
6. (Copilot Reviewer) Some findings were wrong for this milestone. It flagged
   "btn-specijal" as a rule violation, but AGENTS.md allows custom classes with
   the "specijal" prefix in css/style.css. It also reported the missing form
   action and JavaScript handler as bugs, but Milestone 1 pages are static and
   the login is connected to the backend in Milestone 4. I checked the findings
   against AGENTS.md and the milestone requirements and ignored those two.
   The finding that "page-title" has no "specijal" prefix is valid, and I will
   decide whether to rename it. The missing name attributes on the inputs are
   a small valid point that I will handle when the forms are connected.



   ## Milestone 2

   ### Tools and tasks
   - **Claude (claude.ai chat):** generated the first version of the database schema 
   Prompt : I need help with database for my project
   Result : Claude generated the databases and created the bcrypt password hashes which are beyond the lab and requirements

   -**Claude (claude.ai chat):** gave me the terminal command to move the backend folders
   Prompt : What is the command to move files in terminal
   Result : git mv

   ### Where the AI was wrong
   (Claude) It named the DAO files with the Dao ending, but my files were named DAO. I
   noticed it in git status and renamed them, because file names must match the class
   names.

   (Claude) In Milestone 1 it gave me the folders backend/routes, services and dao,
   but Lab 03 uses backend/rest/... . I noticed it when I read the lab and moved
   the folders with git mv.

   (Claude) In Milestone 3, there is no need for hashes, Claude created them either way.


   ## Milestone 3


   ### Tools and tasks
   **Claude (claude.ai chat):** gave me all terminal commands, Git commands
   Prompt: "I need to terminal commands"
   Result: Claude send all the commands I need each time for terminal

   **Claude (claude.ai chat):** gave me tests for Postman
   Prompt: "About the postman and testing, I wanna test everything at the end of the milestone"
   Result: Claude generated all the tests for Postman

   **Claude (claude.ai chat):** suggested the weather API : Open-Meteo and wrote the features
   Prompt: "lets use the weather API, whatever is easier, I just want to use different type of message for each temperature"
   Result: Claude generated the code for weather API and explained its use

   ### Where the AI was wrong
   (Claude) The service test used random e-mail :"AMAR@example.com" for the duplicate check, but my sample data had other e-mails, so the line printed OK and created a user. I noticed the wrong "OK" in the output, and the test was fixed to use my data.

   (Claude) The Git instructions used a plain "git push", which failed with "no upstream branch", because milestone3 was not linked to GitHub yet. I used git push --set-upstream origin milestone3.
   

   ## Milestone 4