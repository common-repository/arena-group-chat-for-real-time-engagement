import { useTheme } from "next-themes";
import { Toaster as Sonner } from "sonner";

type ToasterProps = React.ComponentProps<typeof Sonner>;

const Toaster = ({ ...props }: ToasterProps) => {
  const { theme = "system" } = useTheme();

  return (
    <Sonner
      theme={theme as ToasterProps["theme"]}
      className="tw-toaster tw-group"
      toastOptions={{
        classNames: {
          toast:
            "tw-group tw-toast tw-group-[.toaster]:bg-white tw-group-[.toaster]:text-neutral-950 tw-group-[.toaster]:border-neutral-200 tw-group-[.toaster]:shadow-lg tw-dark:group-[.toaster]:bg-neutral-950 tw-dark:group-[.toaster]:text-neutral-50 tw-dark:group-[.toaster]:border-neutral-800",
          description:
            "tw-group-[.toast]:text-neutral-500 tw-dark:group-[.toast]:text-neutral-400",
          actionButton:
            "tw-group-[.toast]:bg-neutral-900 tw-group-[.toast]:text-neutral-50 tw-dark:group-[.toast]:bg-neutral-50 tw-dark:group-[.toast]:text-neutral-900",
          cancelButton:
            "tw-group-[.toast]:bg-neutral-100 tw-group-[.toast]:text-neutral-500 tw-dark:group-[.toast]:bg-neutral-800 tw-dark:group-[.toast]:text-neutral-400",
        },
      }}
      {...props}
    />
  );
};

export { Toaster };
