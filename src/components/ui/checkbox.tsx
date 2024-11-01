import * as React from "react";
import * as CheckboxPrimitive from "@radix-ui/react-checkbox";
import { Check } from "lucide-react";

import { cn } from "../../lib/utils";

const Checkbox = React.forwardRef<
  React.ElementRef<typeof CheckboxPrimitive.Root>,
  React.ComponentPropsWithoutRef<typeof CheckboxPrimitive.Root>
>(({ className, ...props }, ref) => (
  <CheckboxPrimitive.Root
    ref={ref}
    className={cn(
      "tw-peer tw-h-4 tw-w-4 tw-shrink-0 tw-rounded-sm tw-border tw-border-neutral-200 tw-border-neutral-900 tw-ring-offset-white tw-focus-visible:tw-outline-none tw-focus-visible:tw-ring-2 tw-focus-visible:tw-ring-neutral-950 tw-focus-visible:tw-ring-offset-2 tw-disabled:tw-cursor-not-allowed tw-disabled:tw-opacity-50 tw-data-[state=checked]:tw-bg-neutral-900 tw-data-[state=checked]:tw-text-neutral-50 tw-dark:tw-border-neutral-800 tw-dark:tw-border-neutral-50 tw-dark:tw-ring-offset-neutral-950 tw-dark:tw-focus-visible:tw-ring-neutral-300 tw-dark:tw-data-[state=checked]:tw-bg-neutral-50 tw-dark:tw-data-[state=checked]:tw-text-neutral-900",
      className
    )}
    {...props}
  >
    <CheckboxPrimitive.Indicator
      className={cn(
        "tw-flex tw-items-center tw-justify-center tw-text-current"
      )}
    >
      <Check className="tw-w-4 tw-h-4" />
    </CheckboxPrimitive.Indicator>
  </CheckboxPrimitive.Root>
));
Checkbox.displayName = CheckboxPrimitive.Root.displayName;

export { Checkbox };
