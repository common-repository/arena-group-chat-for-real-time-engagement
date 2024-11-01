import { UseFormReturn } from "react-hook-form";
import { z } from "zod";
import { formSchema } from "../types/form";
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "./ui/form";
import { RadioGroupItem, RadioGroup } from "./ui/radio-group";
import { Eye, EyeIcon, EyeOff, EyeOffIcon } from "lucide-react";
import { cn } from "../lib/utils";
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "./ui/tooltip";

export default function ConfigurationDisplayOption({
  form,
  name,
  label,
  Icon,
}: {
  form: UseFormReturn<z.infer<typeof formSchema>>;
  name:
    | "displayOptions.home"
    | "displayOptions.posts"
    | "displayOptions.pages"
    | "displayOptions.archive"
    | "displayOptions.category"
    | "displayOptions.notFound";
  label: string;
  Icon: React.ElementType;
}) {
  return (
    <FormField
      control={form.control}
      name={name}
      render={({ field }) => (
        <FormItem className="tw-flex tw-items-start tw-space-x-4 tw-p-4 tw-bg-muted tw-rounded-lg">
          <Icon className="tw-h-6 tw-w-6 tw-mt-1" />
          <div className="tw-flex-grow tw-space-y-2">
            <FormLabel className="tw-text-base tw-font-medium">
              {label}
            </FormLabel>
            <FormControl>
              <RadioGroup
                value={field.value}
                onValueChange={field.onChange}
                className="tw-flex tw-flex-col tw-space-y-1 tw-mt-3"
              >
                <FormItem className="tw-flex tw-items-center tw-space-x-3 tw-space-y-0">
                  <FormControl>
                    <RadioGroupItem value="global" />
                  </FormControl>
                  <FormLabel>Use Global Setting</FormLabel>
                </FormItem>
                <FormItem className="tw-flex tw-items-center tw-space-x-3 tw-space-y-0">
                  <FormControl>
                    <RadioGroupItem value="show" />
                  </FormControl>
                  <FormLabel>Always Show</FormLabel>
                </FormItem>
                <FormItem className="tw-flex tw-items-center tw-space-x-3 tw-space-y-0">
                  <FormControl>
                    <RadioGroupItem value="hide" />
                  </FormControl>
                  <FormLabel>Always Hide</FormLabel>
                </FormItem>
              </RadioGroup>
            </FormControl>
          </div>
          <TooltipProvider>
            <Tooltip>
              <TooltipTrigger asChild>
                <div
                  className={`tw-flex tw-items-center tw-justify-center tw-h-8 tw-w-8 tw-rounded-full ${
                    (field.value === "global" &&
                      form.watch("displayOptions.global") === "show") ||
                    field.value === "show"
                      ? "tw-bg-green-500"
                      : "tw-bg-red-500"
                  } tw-text-white`}
                >
                  {(field.value === "global" &&
                    form.watch("displayOptions.global") === "show") ||
                  field.value === "show" ? (
                    <Eye className="tw-h-5 tw-w-5" />
                  ) : (
                    <EyeOff className="tw-h-5 tw-w-5" />
                  )}
                </div>
              </TooltipTrigger>
              <TooltipContent>
                <p>
                  Current setting:{" "}
                  {(field.value === "global" &&
                    form.watch("displayOptions.global") === "show") ||
                  field.value === "show"
                    ? "Shown"
                    : "Hidden"}
                </p>
              </TooltipContent>
            </Tooltip>
          </TooltipProvider>
        </FormItem>
      )}
    />
  );
}
