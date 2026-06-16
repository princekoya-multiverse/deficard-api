#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# DeFiCard API — One-Command Deploy Script
# ═══════════════════════════════════════════════════════════════
# Run this on any Linux server with Docker installed:
#   curl -fsSL https://raw.githubusercontent.com/princekoya-multiverse/deficard-api/master/deploy.sh | bash
# ═══════════════════════════════════════════════════════════════

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${CYAN}"
echo "╔═══════════════════════════════════════════════════════╗"
echo "║       🚀 DeFiCard API — Automated Deployment         ║"
echo "╚═══════════════════════════════════════════════════════╝"
echo -e "${NC}"

# ─── Check Prerequisites ───────────────────────────────────────
echo -e "${YELLOW}[1/6] Checking prerequisites...${NC}"

if ! command -v docker &> /dev/null; then
    echo -e "${RED}Error: Docker is not installed. Install it first:${NC}"
    echo "  curl -fsSL https://get.docker.com | sh"
    exit 1
fi

if ! docker info &> /dev/null; then
    echo -e "${RED}Error: Docker daemon is not running.${NC}"
    exit 1
fi

if ! command -v git &> /dev/null; then
    echo -e "${RED}Error: Git is not installed.${NC}"
    exit 1
fi

echo -e "${GREEN}  ✅ All prerequisites met${NC}"

# ─── Clone Repository ──────────────────────────────────────────
echo -e "${YELLOW}[2/6] Cloning repository...${NC}"

DEPLOY_DIR="/opt/deficard-api"
if [ -d "$DEPLOY_DIR" ]; then
    echo "  Found existing deployment at $DEPLOY_DIR"
    cd "$DEPLOY_DIR"
    git pull origin master
else
    git clone https://github.com/princekoya-multiverse/deficard-api.git "$DEPLOY_DIR"
    cd "$DEPLOY_DIR"
fi

echo -e "${GREEN}  ✅ Repository cloned${NC}"

# ─── Configure Environment ─────────────────────────────────────
echo -e "${YELLOW}[3/6] Configuring environment...${NC}"

if [ ! -f .env ]; then
    cp .env.example .env
    
    # Generate app key
    APP_KEY=$(openssl rand -base64 32 | tr -d '\n')
    sed -i "s/APP_KEY=.*/APP_KEY=base64:$APP_KEY/" .env
    
    # Generate admin password
    ADMIN_PASS=$(openssl rand -base64 12 | tr -d '\n' | tr '+' 'x' | tr '/' 'z')
    sed -i "s/ADMIN_PASSWORD=.*/ADMIN_PASSWORD=$ADMIN_PASS/" .env
    
    echo -e "${GREEN}  ✅ .env file created${NC}"
    echo -e "${YELLOW}  ⚠️  Edit .env to add your NECard and Heleket API keys${NC}"
    echo "  nano .env"
else
    echo -e "${GREEN}  ✅ .env already exists${NC}"
fi

# ─── Build & Start Containers ──────────────────────────────────
echo -e "${YELLOW}[4/6] Building Docker containers...${NC}"

# Remove old containers if they exist
docker compose down 2>/dev/null || true

# Build and start
docker compose up -d --build

echo -e "${GREEN}  ✅ Containers started${NC}"

# ─── Install Dependencies & Run Migrations ─────────────────────
echo -e "${YELLOW}[5/6] Installing dependencies and running migrations...${NC}"

# Wait for MySQL to be ready
echo "  Waiting for MySQL..."
sleep 15

# Install composer dependencies
docker compose exec -T app composer install --no-interaction --prefer-dist 2>&1 | tail -3

# Generate app key if needed
docker compose exec -T app php artisan key:generate --force 2>&1 | tail -1

# Run migrations
docker compose exec -T app php artisan migrate --force 2>&1 | tail -5

# Seed admin user and API key
docker compose exec -T app php artisan db:seed --force 2>&1

echo -e "${GREEN}  ✅ Dependencies installed and migrations run${NC}"

# ─── Final Configuration ───────────────────────────────────────
echo -e "${YELLOW}[6/6] Finalizing...${NC}"

# Create storage link
docker compose exec -T app php artisan storage:link 2>/dev/null || true

# Cache config for production
docker compose exec -T app php artisan config:cache 2>/dev/null || true

# Get the admin API key from database
API_KEY=$(docker compose exec -T app php artisan tinker --execute="echo \App\Models\User::where('email','admin@deficards.io')->first()->tokens()->where('name','hermes-orchestrator')->first()->plainTextToken ?? 'RUN SEEDER FIRST';" 2>/dev/null)

echo ""
echo -e "${CYAN}╔═══════════════════════════════════════════════════════╗"
echo -e "║           🎉 DEPLOYMENT COMPLETE!                      ║"
echo -e "╚═══════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  ${GREEN}API URL:${NC}       http://$(curl -s ifconfig.me 2>/dev/null || hostname -I | awk '{print $1}'):80/api/v1"
echo -e "  ${GREEN}Admin Login:${NC}   admin@deficards.io"
echo -e "  ${GREEN}Admin Password:${NC} $ADMIN_PASS"
echo ""
echo -e "  ${YELLOW}Next Steps:${NC}"
echo "  1. Edit .env with your NECard API keys:"
echo "     nano $DEPLOY_DIR/.env"
echo "     Then: cd $DEPLOY_DIR && docker compose restart app"
echo ""
echo "  2. Point api.deficards.io to this server's IP"
echo ""
echo "  3. Give Hermes the admin API key (run after first deploy):"
echo "     docker compose exec app php artisan db:seed --force"
echo ""
