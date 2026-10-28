FROM node:22-bookworm-slim

WORKDIR /app

# Cache dependencies layer
COPY package.json package-lock.json ./
RUN npm ci && \
    mkdir -p /app/node_modules && \
    sha256sum package-lock.json | awk '{print $1}' > /app/node_modules/.package-lock.sha256

# Copy project source code
COPY . .

# Pre-build production assets once during image build
RUN npm run build

# Entrypoint script to keep volume in sync with package-lock.json
COPY --chmod=755 docker/vite/docker-entrypoint.sh /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

EXPOSE 5173

CMD ["npm", "run", "dev"]
