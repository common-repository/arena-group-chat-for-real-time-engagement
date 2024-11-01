import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "./ui/card";
import { Button } from "./ui/button";
import { Loader2 } from "lucide-react";

const ConnectCard = ({
  isLoading,
  onConnectClick,
}: {
  isLoading: boolean;
  onConnectClick: () => void;
}) => {
  return (
    <Card>
      <CardHeader>
        <CardTitle>Connect your Arena account to Live Chat</CardTitle>
        <CardDescription>
          Engage with your audience in real-time, boost user interaction, and
          provide instant support through our seamless live chat integration for
          WordPress
        </CardDescription>
      </CardHeader>
      <CardContent>
        <Button onClick={onConnectClick} disabled={isLoading}>
          {isLoading ? (
            <>
              <Loader2 className="tw-mr-2 tw-h-4 tw-w-4 tw-animate-spin" />
              Connecting...
            </>
          ) : (
            "Connect with Arena"
          )}
        </Button>
      </CardContent>
    </Card>
  );
};

export default ConnectCard;
