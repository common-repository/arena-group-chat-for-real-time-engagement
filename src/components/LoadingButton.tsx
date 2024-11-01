import { Button } from "./ui/button";
import { Loader2 } from "lucide-react";

const LoadingButton = () => {
  return (
    <Button disabled>
      <Loader2 className="tw-mr-2 tw-h-4 tw-w-4 tw-animate-spin" />
      Please wait
    </Button>
  );
};

export default LoadingButton;
