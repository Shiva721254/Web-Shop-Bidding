# AI Disclosure Statement
Web Development 2 — Web Shop with Buying & Bidding
Student: Shiva Lamichhane

---

During the development of this project I used Claude (an AI assistant developed by Anthropic) as a development tool throughout the project.

## How AI was used

- Code generation — Claude helped generate boilerplate code for  Vue.js frontend components. I reviewed each generated file, understood its purpose, and decided whether to include, modify, or reject it.

- Architecture decisions — I discussed the project structure with Claude,  the Pinia store design, and the JWT implementation approach. Claude suggested approaches and I evaluated them against the course material.

- Debugging — When errors occurred (such as the `firebase/php-jwt` security advisory issue), I used Claude to help diagnose and resolve them. The JWT implementation was ultimately replaced with a custom HS256 implementation using PHP's built-in `hash_hmac` function.

- Git workflow — Claude assisted in structuring the feature branch workflow (develop → feature branches → main).

## My own contributions

- I formulated the project idea and wrote the project proposal independently.
- I made all final decisions about what features to include and how they aligned with the rubrics.
- I reviewed and tested every feature in the running application.
- I understand the code in this project and can explain how it works, including the JWT token generation, the MVC pattern, Vue Router guards, Pinia state management, and the database schema design.

## Reflection

AI assistance significantly accelerated development. However, I treated it as a coding partner rather than a replacement for understanding. I studied the generated code to understand each pattern — such as how PDO prepared statements prevent SQL injection, how bcrypt password hashing works, and how the bid validation logic enforces auction rules.
