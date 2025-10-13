import Fastify from "fastify";
import fastifyStatic from "@fastify/static";
import { fileURLToPath } from "url";
import { dirname, join } from "path";
import fs from "fs";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const fastify = Fastify({ logger: true });

await fastify.register(fastifyStatic, {
  root: join(__dirname, "..", "public"),
  prefix: "/",
});

const FLAG = fs.readFileSync("/flag.txt").toString();
let rsvps = [];
let nextId = 1;

fastify.post("/api/rsvp", async (request, reply) => {
  const { hostName, attendeeCount, depositPaid } = request.body;

  if (!hostName || hostName.trim().length === 0) {
    reply.code(400);
    return {
      success: false,
      error: "INVALID_HOST_NAME",
      message: "Captain's name is required for the masquerade!",
    };
  }

  if (attendeeCount < 1 || attendeeCount > 15) {
    reply.code(400);
    return {
      success: false,
      error: "INVALID_ATTENDEE_COUNT",
      message: "Crew size must be between 1 and 15 members!",
    };
  }

  const requiresPayment = attendeeCount > 5;

  if (requiresPayment && !depositPaid) {
    reply.code(400);
    return {
      success: false,
      error: "PAYMENT_REQUIRED",
      message: `Ahoy! Your crew of ${attendeeCount} requires payment of ${(attendeeCount - 5) * 25} doubloons for the extra ${attendeeCount - 5} matey(s). Please complete payment to secure your spots at the masquerade!`,
    };
  }

  const rsvp = {
    id: nextId++,
    hostName: hostName.trim(),
    attendeeCount,
    depositPaid,
    requiresPayment,
    extraGuests: Math.max(0, attendeeCount - 5),
    totalCost: Math.max(0, attendeeCount - 5) * 25,
    timestamp: new Date().toISOString(),
    status: "confirmed",
    ticketType: requiresPayment && depositPaid ? "VIP" : "STANDARD",
  };

  rsvps.push(rsvp);

  if (attendeeCount > 5) {
    return {
      success: true,
      flag: FLAG,
      message: `Ahoy! You snuck ${attendeeCount} aboard—here’s your bounty.`,
      rsvp,
    };
  }

  return {
    success: true,
    message: `Welcome Captain ${hostName}! Your crew of ${attendeeCount} member${attendeeCount !== 1 ? "s" : ""} is confirmed for the Midnight Masquerade!`,
    rsvp,
  };
});

const start = async () => {
  try {
    await fastify.listen({ port: 3000, host: "0.0.0.0" });
    console.log("🎃 Halloween Server running on port 3000!");
  } catch (err) {
    fastify.log.error(err);
    process.exit(1);
  }
};

start();
