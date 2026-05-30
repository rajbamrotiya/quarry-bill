How I fixed it for your current build
I ran the following commands in the terminal to remove the quarantine attribute and deep-sign the app, which satisfies Gatekeeper for local testing:

1 xattr -cr "./nativephp/electron/dist/mac-arm64/Quarry Bill.app"
2 codesign --force --deep --sign - "./nativephp/electron/dist/mac-arm64/Quarry Bill.app"
You can now open the app normally from Finder. It is currently running in the background.

  ---

How to solve this permanently
If you are just testing locally:
Instead of using native:build and trying to open the .app from Finder, you should use the development command:

1 npm run native:dev
2 # or
3 php artisan native:serve

If you are building to distribute to other users:
You need to provide an Apple Developer account to properly sign and notarize the app. Add the following variables to your .env file before running native:build again:

1 NATIVEPHP_APPLE_ID="your-apple-id@example.com"
2 NATIVEPHP_APPLE_ID_PASS="your-app-specific-password"
3 NATIVEPHP_APPLE_TEAM_ID="your-team-id"
(Note: You must generate an "App-Specific Password" from your Apple ID account page for the NATIVEPHP_APPLE_ID_PASS field).

Try double-clicking the Quarry Bill.app file now! It should open successfully.
