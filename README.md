# Full Stack Developer Task — Role Based Access Control (RBAC)

A small web app where users log in and get different permissions based on their
**role**. Built with plain **HTML, CSS, JavaScript, PHP and MySQL** — no
frameworks, so every line is easy to read and explain.

---

## 1. The four roles

| Role          | What they can do                                                                 |
|---------------|----------------------------------------------------------------------------------|
| Super Admin   | Full access — can delete any post, any comment, and manage (edit/delete) users.  |
| Moderator     | Can delete any post or comment. **Cannot** manage users.                         |
| Regular User  | Can create posts/comments. Can edit/delete **only their own** posts.             |
| Guest         | Read only. Cannot post or comment.                                               |

### Post rules
- Regular users can create posts.
- A user can update or delete **only their own** posts.

### Comment rules (the tricky part)
If **alice** writes a post and **bob** comments on it:
- **alice** (the post owner) can delete bob's comment.
- **bob** can delete his own comment.
- **charlie** (any other regular user) **cannot** delete bob's comment.

All of this lives in one function, `canDeleteComment()`, in
`includes/auth.php`.

---

## 2. How to run it (XAMPP)

1. Install **XAMPP** and start **Apache** and **MySQL** from the control panel.
2. Copy the whole **`Full Stack Developer Task`** folder into XAMPP's
   `htdocs` folder (usually `C:\xampp\htdocs\`).
3. Create the database: open **phpMyAdmin** (`http://localhost/phpmyadmin`),
   click **Import**, choose `sql/database.sql`, and run it. This creates the
   `rbac_system` database and its tables.
4. Create the demo accounts: open
   `http://localhost/Full%20Stack%20Developer%20Task/setup.php` once.
   This inserts the demo users (with hashed passwords) and a couple of sample
   posts/comments.
5. Go to `http://localhost/Full%20Stack%20Developer%20Task/` and log in.

On the login page you type your **username + password** and **pick your role**
from the dropdown ("login type"). The chosen role must match the account's real
role, otherwise login is refused. Every successful login is saved in the
`login_logs` table, and the Super Admin can review them on the **Login Logs** page.

> If your MySQL has a password, set it in `config/db.php` (`$DB_PASS`).

### Demo accounts (password for all: `password123`)
| Username     | Role          |
|--------------|---------------|
| `superadmin` | Super Admin   |
| `moderator`  | Moderator     |
| `alice`      | Regular User  |
| `bob`        | Regular User  |
| `charlie`    | Regular User  |
| `guest`      | Guest         |

---

## 3. Files and what each one does

```
Full Stack Developer Task/
│
├── config/
│   └── db.php            One database connection, reused everywhere.
│
├── includes/
│   ├── auth.php          Session + ALL permission rules (the heart of the app).
│   ├── header.php        Top bar + menu (menu changes by role).
│   └── footer.php        Closing HTML + loads the JavaScript file.
│
├── css/
│   └── style.css         The black-and-grey dark theme.
│
├── js/
│   └── script.js         "Are you sure?" confirmation before deleting.
│
├── sql/
│   └── database.sql      Creates the database + 3 tables (run in phpMyAdmin).
│
├── setup.php             Run once: creates demo users + sample data.
│
├── index.php             Sends you to login or posts.
├── login.php             Login form: username + password + role; saves login to login_logs.
├── register.php          Sign up and choose your role.
├── logout.php            Ends the session.
│
├── posts.php             Lists all posts.
├── view_post.php         One post + its comments + add-comment box.
├── create_post.php       Form to write a post.
├── edit_post.php         Form to edit your own post.
├── delete_post.php       Action only: deletes a post (with permission check).
│
├── add_comment.php       Action only: saves a comment.
├── delete_comment.php    Action only: deletes a comment (with permission check).
│
├── users.php             Super Admin only: list users, change role, delete.
├── update_role.php       Action only: changes a user's role.
├── delete_user.php       Action only: deletes a user.
└── login_logs.php        Super Admin only: view the saved login history.
```

The pages that "do an action" (delete/update) print no HTML — they do the work
and then redirect. This keeps the code in separate files, one job per file.

---

## 4. The 4 database tables

- **users** — `id, username, password (hashed), role, created_at`
- **posts** — `id, user_id (author), title, content, created_at`
- **comments** — `id, post_id, user_id (author), content, created_at`
- **login_logs** — `id, user_id, username, login_type (the role chosen at login), login_time`
  (one new row is added on every successful login)

`posts.user_id`, `comments.user_id` and `login_logs.user_id` link back to
`users`. The foreign keys use **`ON DELETE CASCADE`**, so deleting a post also
deletes its comments, and deleting a user removes their posts, comments and
login history automatically.

---

## 5. How permissions are enforced (important talking point)

There are **two layers**, and the second one is the real security:

1. **In the UI** — buttons are only shown when the helper function says yes
   (e.g. `canDeletePost($post)`). This is just for a clean look.
2. **On the server** — every action file (`delete_post.php`, `edit_post.php`,
   `delete_comment.php`, `users.php`, ...) runs the **same** permission check
   again before touching the database.

Why both? Because hiding a button does **not** stop someone from typing the URL
or sending a request by hand. The server-side check is what actually protects
the data.

All checks come from one file — `includes/auth.php` — so the rules live in a
single place and are easy to read.

---

## 6. Security basics included

- **Passwords are hashed** with `password_hash()` and checked with
  `password_verify()`. Plain passwords are never stored.
- **Prepared statements** (the `?` placeholders with `bind_param`) are used for
  every query that includes user input, which prevents **SQL injection**.
- **Output is escaped** with `htmlspecialchars()` before printing, which
  prevents **XSS** (someone putting `<script>` in a post/comment).
- **Actions use POST**, not links, and re-check permissions server-side.
- The Super Admin cannot delete or demote their own account (no lock-out).

---

## 7. How to demo each role (quick script for the interview)

1. Log in as **alice**, create a post. Notice you can Edit/Delete your own post.
2. Open alice's post, log out, log in as **bob**, add a comment.
3. As **bob**, you can delete *your* comment but there is no Delete button on
   alice's post.
4. Log in as **charlie** — no Delete button on bob's comment (not his comment,
   not his post).
5. Log in as **alice** again — she *can* delete bob's comment (it's on her post).
6. Log in as **moderator** — can delete any post/comment, but there is no
   "Users" menu.
7. Log in as **superadmin** — can delete anything and open the Users page.
8. Log in as **guest** — everything is read only; no New Post, no comment box.

---

## 8. Possible improvements (good to mention you know these)

- Add a **CSRF token** to every form for extra protection.
- Add **pagination** when there are many posts.
- Show friendly **"access denied"** pages instead of silent redirects.
- Move inline styles into the CSS file.
"# task" 
