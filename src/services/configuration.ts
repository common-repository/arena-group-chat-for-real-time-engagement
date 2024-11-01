import { z } from "zod";
import { Chat, formSchema, Site } from "../types/form";

export async function saveConfiguration(
  input: z.infer<typeof formSchema>,
  site: Site,
  chat: Chat
) {
  try {
    const response = await fetch(ajax_object.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "save_arena_configuration",
        organization: input.organizationId,
        site: JSON.stringify(site),
        default_chat: JSON.stringify(chat),
        display_options: JSON.stringify(input.displayOptions),
        position: input.position,
        nonce: agcfre_data.nonce,
      }),
    });

    if (!response.ok) {
      throw new Error("Failed to fetch chats");
    }

    const data = await response.json();
    if (data.success) {
      console.log("Configuration saved successfully");
    } else {
      throw new Error(data.data);
    }
  } catch (err: unknown) {
    throw new Error(
      err instanceof Error ? err.message : "An unknown error occurred"
    );
  }
}

export async function loadConfiguration() {
  const response = await fetch(ajax_object.ajax_url, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      action: "get_arena_configuration",
      nonce: agcfre_data.nonce,
    }),
  });

  if (!response.ok) {
    throw new Error("Failed to fetch configuration");
  }

  const data = await response.json();
  if (data.success) {
    const config = data.data;
    const input = {
      organizationId: config.organization,
      siteId: config.site.id,
      defaultChatId: config.defaultChat.id,
      displayOptions: config.displayOptions,
      position: config.position,
    };
    return input;
  } else {
    console.error("Error loading configuration:", data.data);
  }
}
