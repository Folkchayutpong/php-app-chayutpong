## วิธีติดตั้งและใช้งาน

### 1. ติดตั้ง Docker

- ดาวน์โหลดและติดตั้ง Docker Desktop:
  - [Docker สำหรับ Windows/macOS](https://www.docker.com/products/docker-desktop)

- ตรวจสอบเวอร์ชันใน Terminal:

```bash
docker --version
docker-compose --version
```

### 2. clone project
```bash
git clone https://github.com/Folkchayutpong/php-app-chayutpong.git
cd php-app-chayutpong
```

### 3. สร้างและรัน container
```bash
docker-compose up -d
```

### 4. ดู website
- http://localhost:8080