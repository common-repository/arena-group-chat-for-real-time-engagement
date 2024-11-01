import { useQuery } from "@tanstack/react-query";
import { loadConfiguration } from "../services/configuration";

export function useConfigurations() {
  const query = useQuery({
    queryKey: ["configurations"],
    queryFn: loadConfiguration,
  });

  return { ...query, configurations: query.data };
}
