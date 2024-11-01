import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "./ui/card";
import { UseFormReturn } from "react-hook-form";
import { z } from "zod";
import { formSchema } from "../types/form";
import ConfigurationDisplayOption from "./ConfigurationDisplayOption";
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "./ui/form";
import { RadioGroup, RadioGroupItem } from "./ui/radio-group";
import {
  AlertTriangle,
  Archive,
  EyeIcon,
  EyeOffIcon,
  FileText,
  FolderTree,
  Globe,
  Home,
  Layers,
} from "lucide-react";
import { Separator } from "./ui/separator";
import { Label } from "./ui/label";
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "./ui/tooltip";
import { Checkbox } from "./ui/checkbox";

export default function ConfigurationDisplay({
  form,
}: {
  form: UseFormReturn<z.infer<typeof formSchema>>;
}) {
  const displayOptions: {
    name:
      | "displayOptions.home"
      | "displayOptions.posts"
      | "displayOptions.pages"
      | "displayOptions.archive"
      | "displayOptions.category"
      | "displayOptions.notFound";
    label: string;
    icon: React.ElementType;
  }[] = [
    { name: "displayOptions.home", label: "Home", icon: Home },
    { name: "displayOptions.posts", label: "Posts", icon: FileText },
    { name: "displayOptions.pages", label: "Pages", icon: Layers },
    { name: "displayOptions.archive", label: "Archive", icon: Archive },
    { name: "displayOptions.category", label: "Category", icon: FolderTree },
    { name: "displayOptions.notFound", label: "404 Page", icon: AlertTriangle },
  ];

  return (
    <Card>
      <CardHeader>
        <CardTitle>Display Settings</CardTitle>
        <CardDescription>
          Configure how the chat will be displayed on your site.
        </CardDescription>
      </CardHeader>
      <CardContent className="tw-flex tw-flex-col tw-gap-4">
        <FormField
          control={form.control}
          name="displayOptions.global"
          render={({ field }) => (
            <FormItem className="tw-flex tw-items-center tw-p-4 tw-space-x-4 tw-rounded-lg tw-bg-muted">
              <Globe className="tw-w-6 tw-h-6" />
              <div className="tw-flex-grow">
                <Label htmlFor="global" className="tw-text-base tw-font-medium">
                  Global Widget Visibility
                </Label>
                <p className="tw-text-sm tw-text-muted-foreground">
                  Set the default visibility for all pages
                </p>
              </div>
              <TooltipProvider>
                <Tooltip>
                  <TooltipTrigger asChild>
                    <div className="tw-flex tw-items-center tw-space-x-2">
                      <FormControl>
                        <Checkbox
                          id="global"
                          checked={field.value === "show"}
                          onCheckedChange={(checked) =>
                            field.onChange(checked ? "show" : "hide")
                          }
                        />
                      </FormControl>
                      <FormLabel
                        htmlFor="global"
                        className="tw-text-sm tw-font-medium tw-cursor-pointer"
                      >
                        {field.value === "show" ? "Show" : "Hide"}
                      </FormLabel>
                    </div>
                  </TooltipTrigger>
                  <TooltipContent>
                    <p>
                      Toggle to {field.value === "show" ? "hide" : "show"} the
                      widget on all pages by default
                    </p>
                  </TooltipContent>
                </Tooltip>
              </TooltipProvider>
            </FormItem>
          )}
        />
        <Separator />
        {displayOptions.map((option) => (
          <ConfigurationDisplayOption
            key={option.name}
            form={form}
            name={option.name}
            label={option.label}
            Icon={option.icon}
          />
        ))}
      </CardContent>
    </Card>
  );
}
