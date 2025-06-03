#!/bin/bash
# Script deployment Docker untuk SMAN1-Girsip di lingkungan produksi

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== Deployment Produksi SMAN1-Girsip dengan Docker ===${NC}"

# Pastikan Docker dan Docker Compose terinstall
if ! command -v docker &> /dev/null || ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}Error: Docker dan Docker Compose harus terinstall${NC}"
    echo -e "${YELLOW}Install Docker: https://docs.docker.com/engine/install/${NC}"
    echo -e "${YELLOW}Install Docker Compose: https://docs.docker.com/compose/install/${NC}"
    exit 1
fi

# Setup environment
echo -e "\n${GREEN}[1/7] Setup environment...${NC}"
if [ ! -f .env ]; then
    cp .env.docker .env
    echo -e "${YELLOW}File .env dibuat dari .env.docker${NC}"
    echo -e "${YELLOW}Silakan edit file .env dengan kredensial yang sesuai${NC}"
    
    # Prompt untuk edit .env
    read -p "Apakah Anda ingin mengedit .env sekarang? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        editor .env || nano .env || vi .env
    fi
else
    echo -e "${YELLOW}File .env sudah ada, lewati pembuatan file${NC}"
fi

# Ubah untuk mode produksi
echo -e "\n${GREEN}[2/7] Menyiapkan konfigurasi produksi...${NC}"
sed -i 's/APP_ENV=local/APP_ENV=production/g' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/g' .env

# Build container produksi
echo -e "\n${GREEN}[3/7] Membangun container Docker untuk produksi...${NC}"
docker-compose -f docker-compose.prod.yml build

# Jalankan container
echo -e "\n${GREEN}[4/7] Menjalankan container Docker...${NC}"
docker-compose -f docker-compose.prod.yml up -d

# Tunggu database siap
echo -e "\n${GREEN}[5/7] Menunggu database siap...${NC}"
echo -e "${YELLOW}Menunggu MySQL siap...${NC}"
sleep 10

# Setup Laravel
echo -e "\n${GREEN}[6/7] Setup aplikasi Laravel...${NC}"
echo -e "${YELLOW}Generating application key...${NC}"
docker-compose -f docker-compose.prod.yml exec -T app php artisan key:generate --no-interaction --force

echo -e "${YELLOW}Running database migrations...${NC}"
docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

echo -e "${YELLOW}Creating storage link...${NC}"
docker-compose -f docker-compose.prod.yml exec -T app php artisan storage:link

echo -e "${YELLOW}Optimizing Laravel...${NC}"
docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

# Info selesai
echo -e "\n${GREEN}[7/7] Setup cron untuk Laravel Scheduler...${NC}"
echo -e "${YELLOW}Untuk menjalankan Laravel Scheduler, tambahkan cron job berikut:${NC}"
echo -e "${YELLOW}* * * * * cd /path/to/project && docker-compose -f docker-compose.prod.yml exec -T app php artisan schedule:run >> /dev/null 2>&1${NC}"
echo -e "${YELLOW}Gunakan 'crontab -e' untuk menambahkan baris di atas${NC}"

echo -e "\n${GREEN}=== Deployment selesai! ===${NC}"
echo -e "${GREEN}Aplikasi dapat diakses di: http://domain-anda${NC}"
echo -e "${YELLOW}Catatan: Pastikan untuk mengkonfigurasi domain di server Nginx Anda${NC}"
echo -e "${YELLOW}Jika menggunakan HTTPS, pastikan sertifikat SSL sudah dikonfigurasi di /docker/nginx/ssl/${NC}"

# Info untuk backup
echo -e "\n${GREEN}=== Informasi Backup Database ===${NC}"
echo -e "${YELLOW}Jalankan perintah berikut untuk backup database:${NC}"
echo -e "${YELLOW}docker-compose -f docker-compose.prod.yml exec db sh -c 'exec mysqldump -u root -p\"\$MYSQL_ROOT_PASSWORD\" \$MYSQL_DATABASE > /docker-entrypoint-initdb.d/backup-\$(date +%Y%m%d).sql'${NC}"
