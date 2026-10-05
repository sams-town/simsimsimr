FROM node:18-alpine

WORKDIR /app

COPY package.json .


RUN npm install --force

COPY . .
COPY .env.Dev .env

EXPOSE 3000

CMD [ "npm", "run", "dev" ]