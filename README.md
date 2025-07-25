# eTest Testing System by UZHOST

## 📘 Grammar Test System (PHP + MySQL)

An interactive web-based English grammar test system with:

* ✅ Admin panel to manage questions
* ✅ User registration and login
* ✅ Difficulty & category filters
* ✅ Score calculation and result history
* ✅ Bulk question upload via CSV

---

### 🚀 Features

* 👥 **User Authentication** (Register/Login/Logout)
* 🧪 **Test Generator** (50 random questions per test)
* 🎯 **Difficulty & Category-based filtering**
* 📊 **Result Tracking** (Scores saved per user)
* 📂 **CSV Bulk Import** for questions
* 🛠️ **Admin Panel** (Add/Edit/Delete questions)

---

### 🛠️ Tech Stack

* PHP (7.4+)
* MySQL / MariaDB
* Bootstrap 5
* PDO for secure DB access

---

### 📦 Installation

1. **Clone or Download**

   ```bash
   git clone https://github.com/your-username/grammar-test.git
   cd grammar-test
   ```

2. **Create MySQL Database**

  3. **Set Up Configuration**

   Edit your database connection in `config/db.php`:

   ```php
   $pdo = new PDO("mysql:host=localhost;dbname=grammar_test", "your_db_user", "your_db_password");
   ```

4. **Import Sample Questions**

   You can use the sample CSV file:

   * Go to `admin/upload_questions.php`
   * Upload `questions_sample.csv` to bulk import questions

---

### 🔐 Admin Login (Optional)

* You can implement admin rights manually by adding an `is_admin` column in the `users` table and checking it in admin routes.

---

### 📁 File Structure

```
/config
  db.php                # DB connection
/user
  login.php             # Login page
  register.php          # Registration page
  logout.php
/admin
  dashboard.php         # Admin home
  upload_questions.php  # CSV upload
  edit_question.php     # Edit/delete questions
/tests
  index.php             # Select test options
  start_test.php        # Load test
  submit_test.php       # Calculate and store results
results.php             # Show past results
```

---

### ✅ To Do

* [ ] Add pagination for question bank
* [ ] User profile page
* [ ] Timer for tests
* [ ] Export results to CSV

---

### 💡 License

MIT License – free to use, modify, and distribute.
