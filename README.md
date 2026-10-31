# Mexue Beverage Online Ordering System (Java OOP) 🧋☕

[![Language](https://img.shields.io/badge/Language-Java-orange.svg)](https://www.oracle.com/java/)
[![Paradigm](https://img.shields.io/badge/Paradigm-Object--Oriented%20Programming-blue.svg)]()
[![Interface](https://img.shields.io/badge/Interface-ANSI%20CLI%20Terminal-lightgrey.svg)]()

A comprehensive Java console-based online shopping cart and beverage ordering platform designed for the Mexue franchise. The system models a production-grade retail purchasing pipeline, supporting custom item preparation, dynamic stock tracking, tiered cart operations, multi-channel payment processing, and receipt generation.

---

## ☕ Core OOP Principles Implemented

* **Inheritance:** Hierarchical user model rooted at an abstract `User` base class, extended by `Customer`, `Admin`, and `Manager` child classes to share core authentication properties while isolating specialized behaviors.
* **Polymorphism:**
  * **Polymorphic Arrays:** Manages system users and multi-option payment strategies (`Payment` extended into `OnlineBanking`, `CreditDebitCard`, `TngEwallet`, and `CashOnDelivery`).
  * **Method Overriding:** Subclasses override payment calculation routines (e.g., credit card surcharge adjustments) and purchase summary displays.
* **Encapsulation:** Enforces data hiding via `private` access modifiers across entity attributes, accessed through calibrated getters, setters, and descriptive `toString()` methods.
* **Abstraction & Interfaces:** Implements `Cloneable` on order item entities to enable safe deep cloning during shopping cart operations without mutating catalogue references.

---

## 👤 My Subsystem Contributions: Menu Management Subsystem

I designed and engineered the **Menu Management Subsystem**, which acts as the customer-facing inventory showcase and ordering driver:

* **Categorized Catalogue Browsing:** Organizes beverage categories (Coffee, Milk Tea, Fruit Tea, Chocolate) with real-time stock-out indicators (highlighted in red).
* **Keyword Search Engine:** Allows users to query the menu using sub-string matching, returning real-time filtered results.
* **Custom Beverage Customization:** Prompts users to define customized preparation attributes (Hot/Cold serving types, calibrated sugar levels: 0%, 25%, 50%, 75%, 100%) and quantities before committing items to the cart.
* **CLI Terminal Polish:** Implemented custom ANSI escape sequences for clean screen clears, aligned ASCII banner art, and status transition delays.

---

## 💻 Sample Terminal Interface

```
============================================================
                         M E N U
============================================================
 [a] What's New | [b] Coffee | [c] Milk Tea | [d] Fruit Tea
------------------------------------------------------------
 No.  Item Name                     Price (RM)   Status
 1.   Peach Oolong Tea              RM 7.99      AVAILABLE
 2.   Americano                     RM 6.99      OUT OF STOCK
 3.   Brown Sugar Pearl Milk Tea    RM 8.99      AVAILABLE
------------------------------------------------------------
 Options: [1] Choose Item | [2] View Details | [3] Search | [4] Cart
============================================================
```

---

## 🚀 How to Run

1. Clone the repository:
   ```bash
   git clone https://github.com/Iz040622/Mexue-Beverage-Ordering-System.git
   cd Mexue-Beverage-Ordering-System
   ```
2. Compile all source files:
   ```bash
   javac -d bin src/*.java
   ```
3. Run the application:
   ```bash
   java -cp bin Main
   ```

---

## 👨‍💻 Project Team
* **Isaac Lim Tian Le** — *Menu Management Subsystem, Search Module, CLI Formatting*
* **Chin Jia Jun** — *Customer Management Subsystem, Dynamic Array Architecture*
* **Lau Aik Hung** — *Shopping Cart Subsystem, Staff Management*
* **Keshandra A/L Jayaselan** — *Order & Checkout Subsystem, File I/O*