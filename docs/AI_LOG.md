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