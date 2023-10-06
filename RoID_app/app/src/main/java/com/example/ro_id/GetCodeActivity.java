package com.example.ro_id;

import static java.lang.Integer.parseInt;

import java.io.BufferedReader;
import java.io.IOException;

import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.Headers;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;
import okhttp3.ResponseBody;
import okio.BufferedSink;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.content.ContextCompat;
import androidx.preference.PreferenceManager;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.os.StrictMode;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.TextView;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLConnection;

import javax.net.ssl.HttpsURLConnection;


public class GetCodeActivity extends AppCompatActivity {

    protected String att_id, challenge, code, ip, os, browser, result;
    com.google.android.material.button.MaterialButton Btn_abort;

    final OkHttpClient client = new OkHttpClient().newBuilder().build();

    TextView code_tv, dev_tv, ip_tv;


    Button btn_refresh;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_get_code);
        Bundle bundle = getIntent().getExtras();
        att_id = bundle.getString("att_id");
        challenge = bundle.getString("challenge");

        Window window = this.getWindow();
        window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);
        window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            window.setStatusBarColor(ContextCompat.getColor(this,R.color.white));
        }

        SharedPreferences sharedPreferences =
                PreferenceManager.getDefaultSharedPreferences(this /* Activity context */);
        String name = sharedPreferences.getString("roid_address", "");
        if(name=="")
        {
            name = "10.15.11.233";
        }
        btn_refresh = findViewById(R.id.button3);
        String finalName1 = name;
        btn_refresh.setOnClickListener(v->{
            int x;
            try {
                x = getStatus(finalName1, att_id);
            } catch (IOException e) {
                x=0;
            }
            if(x==1)
            {
                Intent i = new Intent(GetCodeActivity.this, PinActivity.class);
                Bundle bundle2 = new Bundle();
                bundle.putString("att_id", att_id);
                bundle.putString("challenge", challenge);
                i.putExtras(bundle);
                startActivity(i);
                //go to pin activity
            }
        });
        if (Build.VERSION.SDK_INT > 9) {
            StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
            StrictMode.setThreadPolicy(policy);
        }
        Btn_abort = findViewById(R.id.btn_abort);
        String finalName = name;
        Btn_abort.setOnClickListener(v->{
            //abort
            abortLogin(finalName, att_id);
            finish();
        });



        //call api, get code and data
        String result="";
        try{
            String url = "http://" + name + ":8080/get_code?att_id=" + att_id;
            result = getLogin(name,att_id);
        }
        catch (Exception e)
        {
            //abortLogin(name, att_id);
            finish();
            return;
        }

        JSONObject jObject = null;
        try {
            jObject = new JSONObject(result);
        } catch (JSONException e) {
            abortLogin(name, att_id);
            finish();
            return;
        }
        try {
            ip = jObject.getString("ip");
            os = jObject.getString("os");
            browser = jObject.getString("browser");
            code = jObject.getString("code");
        } catch (JSONException e) {
            abortLogin(name, att_id);
            finish();
            return;
        }

        code_tv = findViewById(R.id.textView8);
        code_tv.setText(code);
        dev_tv = findViewById(R.id.textView5);
        dev_tv.setText(os + " | " + browser);
        ip_tv = findViewById(R.id.textView6);
        ip_tv.setText(ip);

    }

    private void abortLogin(String addr, String attempt_id)
    {
        String urlString = "http://" + addr + ":8080/abort?att_id=" + attempt_id;


        //Log.d("DEBUG", "HTTP Request");
        HttpURLConnection urlConnection;
        String response="";
        try {
            URL url = new URL(urlString);
            urlConnection = (HttpURLConnection) url.openConnection();
            InputStream in = new BufferedInputStream(urlConnection.getInputStream());
            response = readStream(in);
            //Log.d("DEBUG", response);
        } catch (Exception e ) {
            System.out.println(e.getMessage());
        }

        return;
    }

    /*private String getDataFromUrl(String demoIdUrl) {

        String error = null;
        String result = null;
        int resCode;
        InputStream in;
        try {
            URL url = new URL(demoIdUrl);
            URLConnection urlConn = url.openConnection();

            HttpsURLConnection httpsConn = (HttpsURLConnection) urlConn;
            httpsConn.setAllowUserInteraction(false);
            httpsConn.setInstanceFollowRedirects(true);
            httpsConn.setRequestMethod("GET");
            httpsConn.connect();
            resCode = httpsConn.getResponseCode();

            if (resCode == HttpURLConnection.HTTP_OK) {
                in = httpsConn.getInputStream();

                BufferedReader reader = new BufferedReader(new InputStreamReader(
                        in, "iso-8859-1"), 8);
                StringBuilder sb = new StringBuilder();
                String line;
                while ((line = reader.readLine()) != null) {
                    sb.append(line).append("\n");
                }
                in.close();
                result = sb.toString();
            } else {
                error += resCode;
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
        return result;
    }*/
    String getLogin(String addr, String attempt_id) throws IOException {
        String url = "http://" + addr + ":8080/get_code?att_id=" + attempt_id;
        Request request = new Request.Builder()
                .url(url)
                .build();

        try (Response response = client.newCall(request).execute()) {
            return response.body().string();
        }
        catch (IOException e)
        {
            e.printStackTrace();
            return "";
        }
    }

    int getStatus(String addr, String attempt_id) throws IOException {
        String url = "http://" + addr + ":8080/get_paired_status?att_id=" + attempt_id;
        Request request = new Request.Builder()
                .url(url)
                .build();

        try (Response response = client.newCall(request).execute()) {
            return parseInt(response.body().string());
        }
        catch (IOException e)
        {
            e.printStackTrace();
            return 0;
        }
    }

    /*private String getCode(String addr, String att_id) throws IOException {
        String urlString = "http://" + addr + ":8080/get_code?att_id=" + att_id;
        URL url = new URL(urlString);
        String response="";
        HttpURLConnection urlConnection = (HttpURLConnection) url.openConnection();
        try {
            InputStream in = new BufferedInputStream(urlConnection.getInputStream());
            response = readStream(in);
        } finally {
            urlConnection.disconnect();
        }
        /*
        //Log.d("DEBUG", "HTTP Request");
        HttpURLConnection urlConnection;
        String response="";
        try {
            URL url = new URL(urlString);
            urlConnection = (HttpURLConnection) url.openConnection();
            InputStream in = new BufferedInputStream(urlConnection.getInputStream());
            response = readStream(in);
            //Log.d("DEBUG", response);
        } catch (Exception e ) {
            System.out.println(e.getMessage());
        }

        return response;
    }*/

    private String readStream(InputStream is) {
        try {
            ByteArrayOutputStream bo = new ByteArrayOutputStream();
            int i = is.read();
            while(i != -1) {
                bo.write(i);
                i = is.read();
            }
            return bo.toString();
        } catch (IOException e) {
            return "";
        }
    }
}