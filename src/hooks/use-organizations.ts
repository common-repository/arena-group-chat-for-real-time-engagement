import { useQuery } from "@tanstack/react-query";
import { fetchOrganizations } from "../services/organizations";

export function useOrganizations() {
  const { data, isLoading, error } = useQuery({
    queryKey: ["organizations"],
    queryFn: () => fetchOrganizations(),
  });

  return { organizations: data, isLoading, error };
}
