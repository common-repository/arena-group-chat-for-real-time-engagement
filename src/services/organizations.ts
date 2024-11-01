import { Organization } from "../types/form";

export async function fetchOrganizations() {
  try {
    const response = await fetch(ajax_object.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "fetch_arena_organizations",
        nonce: agcfre_data.nonce,
      }),
    });

    if (!response.ok) {
      throw new Error("Failed to fetch organizations");
    }

    const data = await response.json();
    if (data.success) {
      return data.data.organizations as Organization[];
    } else {
      throw new Error(data.data);
    }
  } catch (err: unknown) {
    throw new Error(
      err instanceof Error ? err.message : "An unknown error occurred"
    );
  }
}
