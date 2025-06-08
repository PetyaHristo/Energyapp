#include <WiFi.h>
#include <HTTPClient.h>
#include <time.h>

const char* ssid = "UKTC";
const char* password = "uktc1234";
const char* serverName = "http://localhost/vscode/energy_app/receive_data.php";

int year, month;
int day = 1; // започваме от 1

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);

  Serial.print("Свързване към WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nСвързано успешно!");

  // Настройка на време чрез NTP
  configTime(0, 0, "pool.ntp.org", "time.nist.gov");

  struct tm timeinfo;
  Serial.print("Изчакване за време от NTP");
  while (!getLocalTime(&timeinfo)) {
    Serial.print(".");
    delay(500);
  }
  Serial.println("\nВремето е успешно синхронизирано");

  // Запазваме текущата година и месец
  year = timeinfo.tm_year + 1900;
  month = timeinfo.tm_mon + 1;
}

void loop() {
  struct tm timeinfo;
  if (!getLocalTime(&timeinfo)) {
    Serial.println("Неуспешно получаване на време");
    delay(10000);
    return;
  }

  int currentDay = timeinfo.tm_mday;

  if (day > currentDay) {
    Serial.println("Всички дни до днешна дата са изпратени.");
    while (true) {
      delay(1000); // Спиране на изпълнението
    }
  }

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    float energy = random(100, 500) / 10.0;

    String url = String(serverName) + "?user_id=6&year=" + year + "&month=" + month + "&day=" + day + "&energy=" + energy;

    Serial.println("Изпращане към: " + url);

    http.begin(url);
    int httpResponseCode = http.GET();

    Serial.print("HTTP отговор: ");
    Serial.println(httpResponseCode);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("Отговор от сървъра: " + response);
    } else {
      Serial.println("Грешка при HTTP заявката");
    }

    http.end();
    day++;
  } else {
    Serial.println("WiFi не е свързан");
  }

  delay(10000);
}
