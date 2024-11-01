import { Chat } from "../types/form";

export async function fetchChats(siteId: string) {
  try {
    const response = await fetch(ajax_object.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "fetch_arena_chats",
        site_id: siteId || "",
        nonce: agcfre_data.nonce,
      }),
    });

    if (!response.ok) {
      throw new Error("Failed to fetch chats");
    }

    const data = await response.json();
    if (data.success) {
      return data.data.chats as Chat[];
    } else {
      throw new Error(data.data);
    }
  } catch (err: unknown) {
    throw new Error(
      err instanceof Error ? err.message : "An unknown error occurred"
    );
  }
}
