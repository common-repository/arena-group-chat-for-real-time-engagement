import { Site } from "../types/form";

export async function fetchSites(organizationId: string) {
  try {
    const response = await fetch(ajax_object.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "fetch_arena_sites",
        organization_id: organizationId || "",
        nonce: agcfre_data.nonce,
      }),
    });

    if (!response.ok) {
      throw new Error("Failed to fetch sites");
    }

    const data = await response.json();
    if (data.success) {
      return data.data.sites as Site[];
    } else {
      throw new Error(data.data);
    }
  } catch (err: unknown) {
    throw new Error(
      err instanceof Error ? err.message : "An unknown error occurred"
    );
  }
}
