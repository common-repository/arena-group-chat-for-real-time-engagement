import OrganizationSelect from "./OrganizationSelect";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "./ui/card";
import SiteSelect from "./SiteSelect";
import DefaultChatSelect from "./DefaultChatSelect";
import { UseFormReturn } from "react-hook-form";
import { z } from "zod";
import { formSchema } from "../types/form";
import PositionSelect from "./PositionSelect";

export default function ConfigrationArena({
  form,
}: {
  form: UseFormReturn<z.infer<typeof formSchema>>;
}) {
  return (
    <Card>
      <CardHeader>
        <CardTitle>Arena Configuration</CardTitle>
        <CardDescription>
          Configure your Arena Live Chat settings here.
        </CardDescription>
      </CardHeader>
      <CardContent className="tw-flex tw-flex-col tw-gap-4">
        <OrganizationSelect form={form} />
        <SiteSelect form={form} />
        <DefaultChatSelect form={form} />
        <PositionSelect form={form} />
      </CardContent>
    </Card>
  );
}
