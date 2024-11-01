import { useSites } from "./use-sites";
import { Site } from "../types/form";
import { useMemo } from "react";

export function useSite(organizationId: string, siteId: string) {
  const { sites, isLoading, error } = useSites(organizationId);

  const site = useMemo(
    () => sites?.find((site: Site) => site.id === siteId),
    [sites, siteId]
  );

  return { site, isLoading, error };
}
