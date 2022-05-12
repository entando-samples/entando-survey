FROM node:16
ARG SOURCE_ROOT="./src/main/node"
# Create app directory
WORKDIR /usr/src/app
# Install app dependencies
# A wildcard is used to ensure both package.json AND package-lock.json are copied
# where available (npm@5+)
COPY $SOURCE_ROOT/package*.json ./

RUN npm install
# If you are building your code for production
# RUN npm ci --only=production

# Bundle app source
COPY $SOURCE_ROOT .

EXPOSE 8081

CMD [ "node", "bin/www" ]


