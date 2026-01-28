# Docker Quick Reference

## 🚀 Quick Start (Copy & Paste)

### First Time - Linux/Mac
```bash
cd leave-management
cp .env.docker .env
./docker-start.sh
```

### First Time - Windows
```cmd
cd leave-management
copy .env.docker .env
docker-start.bat
```

### Access
- App: http://localhost:8000
- PHPMyAdmin: http://localhost:8080
- Login: admin@company.com / admin123

---

## 📋 Essential Commands

| Task | Command |
|------|---------|
| **Start** | `docker-compose up -d` |
| **Stop** | `docker-compose down` |
| **View Logs** | `docker-compose logs -f` |
| **App Shell** | `docker-compose exec app bash` |
| **MySQL Shell** | `docker-compose exec mysql mysql -u leave_user -p leave_management` |
| **Rebuild** | `docker-compose up -d --build` |
| **Fresh Start** | `docker-compose down -v && docker-compose up -d --build` |
| **Status** | `docker-compose ps` |

---

## 🐛 Troubleshooting

**Containers won't start?**
```bash
docker-compose logs
```

**Can't access application?**
```bash
docker-compose ps
# All should show "Up"
```

**Port already in use?**
Edit `.env` and change `APP_PORT` or `PHPMYADMIN_PORT`, then:
```bash
docker-compose down
docker-compose up -d
```

**MySQL connection fails?**
```bash
# Wait for MySQL to initialize
sleep 20
docker-compose restart app
```

**Need clean slate?**
```bash
docker-compose down -v
docker-compose up -d
```

---

## 📦 What's Running

| Service | Port | Purpose |
|---------|------|---------|
| PHP App | 8000 | Main application |
| MySQL | 3306 | Database |
| PHPMyAdmin | 8080 | Database UI |

---

## 💾 Development Tips

✅ **Files change instantly** - Edit PHP/JS files and refresh browser  
✅ **Database included** - No separate MySQL install needed  
✅ **PHPMyAdmin included** - Easy database management  
✅ **Isolated environment** - Won't affect your system  
✅ **Easy to clean up** - One command removes everything  

---

## 📚 Full Documentation

See [DOCKER.md](./DOCKER.md) for comprehensive guide.
