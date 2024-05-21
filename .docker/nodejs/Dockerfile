FROM node:14

ARG NODE_ENV

WORKDIR /app

# maybe add a volume for the app root and build befor CMD

EXPOSE 3000

CMD NODE_ENV=development yarn install; \
if [ $NODE_ENV = 'production' ]; \
then \
  NODE_ENV=development yarn build; \
  yarn start; \
else \
  yarn dev; \
fi
