# 🌐 PHP Styled Homepage

MVC 구조 + Service 계층 + MySQL 연동 + 간단한 오토로딩 시스템으로 구성된 경량 PHP 홈페이지입니다.

---

## 🧱 프로젝트 구조

```
.
├── index.php                     # 진입점
├── Router.php                    # 오토로딩 + 라우팅 처리기
├── helpers.php                   # view() 함수 정의
├── layouts/                      # header/footer 공통 레이아웃
├── pages/                        # HTML 템플릿 페이지
│   ├── home.php
│   ├── blog.php
│   ├── gallery.php
│   ├── register.php             # 회원가입 폼
│   └── login.php                # 로그인 폼
├── assets/css/style.css         # 기본 스타일
├── routes/web.php               # 라우트 정의
├── app/
│   ├── Http/Controllers/        # 컨트롤러
│   │   └── RegisterController.php
│   ├── Services/                # 비즈니스 로직 계층
│   │   └── RegisterService.php
│   └── Models/                  # DB 모델
│       └── User.php
```

---

## 🚀 실행 방법1

```bash
php -S localhost:8000
```

접속 → http://localhost:8000

---


### 📄 DB 테이블 생성

```sql
CREATE DATABASE IF NOT EXISTS homepage CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE homepage;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## ✅ 기능

| 기능 | 설명 |
|------|------|
| 홈 | 홈페이지 소개 |
| 블로그 | 리스트형 게시글 |
| 썸네일 갤러리 | 카드 UI 기반 이미지 |
| 회원가입 | 이메일+비밀번호 DB 저장 |
| 로그인 (예정) | 세션 인증 처리 예정 |
| 뷰 렌더링 | `view('home')` 방식으로 뷰 호출 |
| 오토로딩 | PSR-4 유사 규칙 기반 로딩 (`App\` → `app/`) |

---

## 🧠 구조 철학

- **Controller는 흐름 제어만**
- **Service는 로직만**
- **Model은 DB처리 전담**
- **View는 깔끔하게 분리**
- Composer 없이도 작동 가능한 실용적인 MVC 기반

---

## ⚙️ 향후 발전 방향

- 로그인 기능 (세션)
- 유효성 검증 클래스 분리 (Request 계층)
- 게시글 CRUD
- 파일 업로드 (갤러리)
- 댓글/좋아요 등 사용자 인터랙션

---

## 📌 만든 이유

> 아주 가볍게 시작할 수 있는 구조지만,
> MVC와 서비스 계층 분리, MySQL 연동, 오토로딩 등 실전적인 PHP 구조 학습을 위한 구조입니다.

---

## 🤝 License

MIT
