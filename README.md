# 🌟 MyLife Hub - PHP Personal Homepage

가벼운 MVC 구조와 서비스 계층을 갖춘 PHP 개인 홈페이지 예제입니다. TO-DO 리스트, 회원 게시판, 로그인/회원가입/마이페이지 메뉴를 중심으로 개인 활동을 관리할 수 있습니다.

---

## 🧱 프로젝트 구조

```
.
├── index.php                     # 진입점
├── Router.php                    # 라우팅 처리기
├── helpers.php                   # 공통 헬퍼 (뷰 렌더링, 세션 유틸 등)
├── layouts/                      # 헤더/푸터 레이아웃
├── pages/                        # 화면 템플릿
│   ├── home.php                  # 메인 홈
│   ├── login.php                 # 로그인
│   ├── register.php              # 회원가입
│   ├── mypage.php                # 마이페이지
│   ├── todo/index.php            # TO-DO 리스트 화면
│   └── board/index.php           # 회원 게시판
├── assets/css/style.css          # 공통 스타일
├── routes/web.php                # 웹 라우트 정의
└── app/
    ├── Http/Controllers/         # 컨트롤러 계층
    │   ├── HomeController.php
    │   ├── LoginController.php
    │   ├── LogoutController.php
    │   ├── MyPageController.php
    │   ├── RegisterController.php
    │   ├── TodoController.php
    │   └── BoardController.php
    ├── Models/                   # 모델 정의
    │   ├── User.php
    │   ├── Todo.php
    │   └── BoardPost.php
    ├── Repositories/             # DB 접근 계층
    │   ├── Common/Repository.php
    │   ├── UserRepository.php
    │   ├── TodoRepository.php
    │   └── BoardRepository.php
    └── Services/                 # 비즈니스 로직 계층
        ├── LoginService.php
        ├── RegisterService.php
        ├── TodoService.php
        └── BoardService.php
```

---

## 🚀 실행 방법

```bash
php -S localhost:8000
```

접속 후 `http://localhost:8000` 에서 홈페이지를 확인하세요.

---

## 🗄️ DB 테이블 생성

`config.ini`에 MySQL 정보를 설정한 뒤 아래 스키마를 실행해 주세요.

```sql
CREATE DATABASE IF NOT EXISTS homepage CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE homepage;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(30) DEFAULT 'USER',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS todos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  is_completed TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS board_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## ✅ 제공 기능

| 메뉴 | 설명 |
|------|------|
| TO-DO 리스트 | 로그인 후 개인 할 일을 추가·완료·삭제로 관리 |
| 회원 게시판 | 회원 간 게시글 작성 및 최신 글 확인 |
| 로그인/회원가입 | 이메일 기반 회원 가입 및 인증 |
| 마이페이지 | 나의 할 일 통계와 최근 게시글/할 일 확인 |
| 홈 | 주요 메뉴 안내, 최신 게시글/할 일 미리보기 |

---

## 🧠 설계 철학

- 컨트롤러는 흐름 제어에 집중하고, 서비스 계층은 비즈니스 로직을 담당합니다.
- 레포지토리 계층을 통해 데이터베이스 접근을 캡슐화하여 테스트와 확장을 용이하게 했습니다.
- 뷰는 순수 PHP 템플릿으로 구성하여 간단히 커스터마이징할 수 있습니다.
- Composer 없이도 동작 가능한 경량 MVC 스타일 구조입니다.

---

## 🔮 향후 확장 아이디어

- 댓글 및 좋아요 기능 추가로 커뮤니티 고도화
- 할 일 마감일, 우선순위 속성 추가
- 업로드가 가능한 갤러리 섹션 확장
- 알림/이메일 전송 기능 연동

---

## 🤝 License

MIT
