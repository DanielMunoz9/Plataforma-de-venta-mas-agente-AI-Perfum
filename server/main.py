import os
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from dotenv import load_dotenv
from openai import OpenAI
from .amelie_personality import system_prompt

# Load .env when running locally
load_dotenv()

OPENAI_API_KEY = os.getenv("OPENAI_API_KEY")
if not OPENAI_API_KEY:
    raise RuntimeError("Falta OPENAI_API_KEY en variables de entorno (.env o secret).")

MODEL = os.getenv("AI_MODEL", "gpt-5-mini")
TEMPERATURE = float(os.getenv("TEMPERATURE", "0.9"))
TOP_P = float(os.getenv("TOP_P", "0.95"))
MAX_TOKENS = int(os.getenv("MAX_TOKENS", "500"))

client = OpenAI(api_key=OPENAI_API_KEY)

app = FastAPI(title="Amélie API")

# CORS settings
origins = [o.strip() for o in os.getenv("ALLOW_ORIGINS", "*").split(",")]
app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,
    allow_credentials=True,
    allow_methods=["POST", "OPTIONS", "GET"],
    allow_headers=["*"]
)

class ChatIn(BaseModel):
    text: str

class ChatOut(BaseModel):
    reply: str

@app.get("/api/health")
def health():
    return {"ok": True}

@app.post("/api/amelie", response_model=ChatOut)
def chat(payload: ChatIn):
    resp = client.chat.completions.create(
        model=MODEL,
        temperature=TEMPERATURE,
        top_p=TOP_P,
        max_tokens=MAX_TOKENS,
        messages=[
            {"role": "system", "content": system_prompt()},
            {"role": "user", "content": payload.text},
        ],
    )
    answer = resp.choices[0].message.content.strip()
    return ChatOut(reply=answer)