# Kaspi Backend Service (Laravel API)

Жобаның барлық бизнес-логикасын, деректер қорын және үшінші тарап интеграцияларын реттейтін негізгі бэкэнд қызметі.

## 🛠️ Технологиялық стек және қолданылған терминдер (Tech Lead-ке шолу)

Бұл жоба бэкэнд әзірлеудің маңызды принциптерін көрсетеді:

- **Фреймворк:** Laravel (PHP), REST API архитектурасы (секцияланған маршруттар, мысалы `routes/admin.php`)
- **Контейнерлеу және Инфрақұрылым:** Docker мен `docker-compose.yml` арқылы оқшауланған сервер ортасы
- **Нақты уақыттағы деректер (Real-time):** Websocket (Laravel Event Broadcasting арқылы чат жүйесі)
- **AI Интеграциясы (OpenAI SaaS):** Автоматты түрде чат жүйесін қамтамасыз ететін және дауыстық файлдарды мәтінге айналдыратын (transcribe) OpenAI API интеграциялары
- **Қауіпсіздік (Web Security):** Token-based authentication, CORS саясаты, CSRF қорғанысы, API сұраныстарын валидациялау
- **Инфрақұрылым (DevOps):** CI/CD процестерін баптау
- **Нұсқаларды басқару:** Git

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- Simple, fast routing engine.
- Powerful dependency injection container.
- Multiple back-ends for session and cache storage.
- Expressive, intuitive database ORM.
- Database agnostic schema migrations.
- Robust background job processing.
- Real-time event broadcasting.
