# Stage 1: Build the application
FROM node:18-alpine AS builder

# Set working directory and environment variables
WORKDIR /app
ENV NODE_ENV=production

# Copy package files first to leverage Docker cache
COPY package*.json ./
COPY .npmrc* ./

# Install dependencies (clean cache for smaller image)
RUN npm ci --no-optional && \
    npm cache clean --force

# Copy all source files
COPY . .

# Build the application (adjust if using different build tools)
RUN npm run build

# ----------------------------
# Stage 2: Production server
FROM nginx:1.25-alpine

# Remove default Nginx content
RUN rm -rf /usr/share/nginx/html/*

# Copy built assets from builder
COPY --from=builder /app/dist /usr/share/nginx/html

# Copy custom Nginx config (if needed)
# COPY nginx.conf /etc/nginx/conf.d/default.conf

# Expose port and start Nginx
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]