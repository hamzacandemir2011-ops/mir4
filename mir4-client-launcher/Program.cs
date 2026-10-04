using System.Text;

namespace Mir_4_Launcher
{
    internal static class Program
    {
        /// <summary>
        ///  The main entry point for the application.
        /// </summary>
        [STAThread]
        static void Main()
        {
            // To customize application configuration such as set high DPI settings or default font,
            // see https://aka.ms/applicationconfiguration.
            //ApplicationConfiguration.Initialize();

            // The launcher resolves "Client\..." paths relative to the working directory,
            // so pin it to the exe folder (shortcuts may start it from elsewhere).
            Environment.CurrentDirectory = AppContext.BaseDirectory;

            Application.Run(new Launcher());
        }
    }
}