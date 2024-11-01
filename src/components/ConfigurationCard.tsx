import * as React from "react";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "./ui/card";
import { Button } from "./ui/button";
import ConfigrationArena from "./ConfigrationArena";
import { Form } from "./ui/form";
import { z } from "zod";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { formSchema } from "../types/form";
import ConfigurationDisplay from "./ConfigurationDisplay";
import { usePostConfiguration } from "../hooks/use-post-configuration";
import { useConfigurations } from "../hooks/use-configurations";
import { useSite } from "../hooks/use-site";
import { useChat } from "../hooks/use-chat";
import { Toaster } from "./ui/sonner";

const ConfigurationCard = ({ onDisconnect }: { onDisconnect: () => void }) => {
  const { mutate, isPending } = usePostConfiguration();
  const { configurations } = useConfigurations();
  const [isLoading, setIsLoading] = React.useState(true);
  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      organizationId: "",
      siteId: "",
      defaultChatId: "",
      displayOptions: {
        global: "show",
        home: "global",
        posts: "global",
        pages: "global",
        archive: "global",
        category: "global",
        notFound: "global",
      },
      position: "bottom",
    },
  });
  const { site } = useSite(form.watch("organizationId"), form.watch("siteId"));
  const { chat } = useChat(form.watch("siteId"), form.watch("defaultChatId"));

  React.useEffect(() => {
    if (configurations) {
      if (
        configurations.displayOptions &&
        configurations.displayOptions.global
      ) {
        form.reset(configurations);
      }

      setIsLoading(false);
    }
  }, [configurations]);

  const onSubmit = (data: z.infer<typeof formSchema>) => {
    if (!site || !chat) {
      return;
    }

    mutate({ data, site, chat });
  };

  if (isLoading) {
    return <div>Loading...</div>;
  }

  return (
    <Form {...form}>
      <form
        onSubmit={form.handleSubmit(onSubmit)}
        className="tw-space-y-8 tw-max-w-[600px]"
      >
        <ConfigrationArena form={form} />
        <ConfigurationDisplay form={form} />
        <Button type="submit" disabled={isPending} variant="primary">
          {isPending ? "Saving..." : "Save Settings"}
        </Button>
      </form>

      <Button onClick={onDisconnect} variant="destructive" className="tw-my-3">
        Disconnect
      </Button>
      <Toaster />
    </Form>
  );
};

export default ConfigurationCard;
