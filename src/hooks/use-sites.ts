import { useQuery } from "@tanstack/react-query";
import { fetchSites } from "../services/sites";

export function useSites(organizationId: string) {
  const { data, isLoading, error } = useQuery({
    queryKey: ["sites", organizationId],
    queryFn: () => (organizationId ? fetchSites(organizationId) : []),
  });

  return { sites: data, isLoading, error };
}
