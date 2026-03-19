import { NextResponse } from "next/server";

export async function POST(req: Request) {
  try {
    const { message } = await req.json();
    const apiKey = process.env.GROQ_API_KEY;

    if (!apiKey) return NextResponse.json({ error: "API Key မရှိပါ" }, { status: 500 });

    const response = await fetch("https://api.groq.com/openai/v1/chat/completions", {
      method: "POST",
      headers: {
        "Authorization": `Bearer ${apiKey}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        model: "llama-3.3-70b-versatile", // အရမ်းတော်တဲ့ Model ဖြစ်ပါတယ်
        messages: [
          {
            role: "system",
            content: "You are a professional AI Pharmacist for ZweToe Pharmacy. Answer in Myanmar and English. Always add medical disclaimer."
          },
          {
            role: "user",
            content: message
          }
        ]
      }),
    });

    const data = await response.json();
    
    if (data.error) throw new Error(data.error.message);

    const aiText = data.choices[0].message.content;
    return NextResponse.json({ text: aiText });

  } catch (error: any) {
    console.error("Connection Error:", error.message);
    return NextResponse.json({ error: "AI နှင့် ချိတ်ဆက်၍မရပါ" }, { status: 500 });
  }
}