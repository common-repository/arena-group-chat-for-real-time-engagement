import { useMutation } from "@tanstack/react-query";
import { saveConfiguration } from "../services/configuration";
import { Chat, formSchema, Site } from "../types/form";
import { z } from "zod";
import { toast } from "sonner";

export function usePostConfiguration() {
  const mutation = useMutation({
    mutationFn: ({
      data,
      site,
      chat,
    }: {
      data: z.infer<typeof formSchema>;
      site: Site;
      chat: Chat;
    }) => saveConfiguration(data, site, chat),
    onSuccess: () => {
      toast.success("Configuration saved successfully");
    },
  });

  return mutation;
}
