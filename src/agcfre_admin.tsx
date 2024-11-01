// Create a new file: src/admin.js
// @ts-nocheck
import { render, Component } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";
import "./arena-chat-tool-admin.css";
import { Card, CardContent } from "./components/ui/card";
import ConnectCard from "./components/ConnectCard";
import ConfigurationCard from "./components/ConfigurationCard";
import LoadingButton from "./components/LoadingButton";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

const queryClient = new QueryClient();

class SettingsPage extends Component {
  constructor() {
    super(...arguments);
    this.state = {
      isLoading: false,
      isConnected: false,
      isCheckingStatus: true,
    };
  }

  componentDidMount() {
    this.checkConnectionStatus();
  }

  checkConnectionStatus = async () => {
    this.setState({ isCheckingStatus: true });
    try {
      const response = await apiFetch({
        path: "/arena-group-chat-for-real-time-engagement/v1/connection-status",
        method: "GET",
      });
      this.setState({ isConnected: response.isConnected });
    } catch (error) {
      console.error("Error checking connection status:", error);
    } finally {
      this.setState({ isCheckingStatus: false });
    }
  };

  handleConnectClick = async () => {
    this.setState({ isLoading: true });
    try {
      const response = await apiFetch({
        path: "/arena-group-chat-for-real-time-engagement/v1/generate-token",
        method: "GET",
      });

      if (response && response.token) {
        const token = response.token;
        const url = window.encodeURIComponent(
          `${window.location.origin}/wp-json/arena-group-chat-for-real-time-engagement/v1/activate`
        );
        const product = "group_chat";

        const utms = `utm_source=wordpress&utm_medium=marktplace&utm_campaign=wp_dashboard&utm_content=group-chat`;

        const redirectUrl = `https://app.arena.im/integrations/wordpress?token=${token}&url=${url}&product=${product}&${utms}`;
        window.open(redirectUrl, "_blank").focus();
      } else {
        throw new Error("Failed to generate token");
      }
    } catch (error) {
      console.error("Error generating token:", error);
      alert("Failed to connect with Arena. Please try again.");
    } finally {
      this.setState({ isLoading: false });
    }
  };

  handleDisconnect = async () => {
    this.setState({ isLoading: true });
    try {
      const response = await apiFetch({
        path: "/arena-group-chat-for-real-time-engagement/v1/disconnect",
        method: "POST",
      });

      if (response && response.success) {
        this.setState({ isConnected: false });
        alert("Successfully disconnected from Arena Live Chat.");
      } else {
        throw new Error("Failed to disconnect");
      }
    } catch (error) {
      console.error("Error disconnecting:", error);
      alert("Failed to disconnect from Arena Live Chat. Please try again.");
    } finally {
      this.setState({ isLoading: false });
    }
  };

  render() {
    const { isConnected, isCheckingStatus, isLoading } = this.state;

    if (isCheckingStatus) {
      return (
        <Card>
          <CardContent className="tw-flex tw-justify-center tw-items-center tw-h-40">
            <LoadingButton />
          </CardContent>
        </Card>
      );
    }

    return (
      <QueryClientProvider client={queryClient}>
        <div>
          {isConnected ? (
            <ConfigurationCard onDisconnect={this.handleDisconnect} />
          ) : (
            <ConnectCard
              isLoading={isLoading}
              onConnectClick={this.handleConnectClick}
            />
          )}
        </div>
      </QueryClientProvider>
    );
  }
}

render(<SettingsPage />, document.getElementById("agcfre-settings-root"));
