# Этап 1: Сборка
FROM node:22-alpine AS builder

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

RUN npm run build

# Этап 2: Запуск
FROM node:22-alpine

WORKDIR /app

COPY --from=builder /app/.output ./

EXPOSE 3100

CMD ["node", "server/index.mjs"]
