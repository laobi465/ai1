#!/bin/bash
# 部署 WordPress 内容流水线到 n8n 实例
# 用法:
#   export N8N_JWT="<你的 n8n JWT / API Key>"
#   ./deploy-n8n-workflow.sh <n8n-url>
# 示例:
#   ./deploy-n8n-workflow.sh https://n8n.yourdomain.com
#   ./deploy-n8n-workflow.sh http://localhost:5678

N8N_URL="${1:?用法: $0 <n8n-url>，且需先 export N8N_JWT}"
JWT="${N8N_JWT:?请先 export N8N_JWT 为你的 n8n API Key / JWT}"

echo "Deploying to n8n at: $N8N_URL"

# 尝试用 JWT 作为 Bearer token，走 n8n 新版 public REST API
curl -s -w "\nHTTP %{http_code}\n" -X POST "$N8N_URL/api/v1/workflows" \
  -H "Authorization: Bearer $JWT" \
  -H "Content-Type: application/json" \
  -d @/workspace/wordpress-content-pipeline.json

echo "---"

# 兜底：尝试 n8n 旧版 REST 路径（有些实例 API key 需用 X-N8N-API-KEY 头）
curl -s -w "\nHTTP %{http_code}\n" -X POST "$N8N_URL/api/v1/workflows" \
  -H "X-N8N-API-KEY: $JWT" \
  -H "Content-Type: application/json" \
  -d @/workspace/wordpress-content-pipeline.json

echo ""
echo "若两者都失败：请通过 n8n UI → Workflows → Import from File 手动导入 wordpress-content-pipeline.json"