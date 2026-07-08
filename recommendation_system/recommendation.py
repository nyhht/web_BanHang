import mysql.connector
from mysql.connector import Error
from sqlalchemy import create_engine
import pandas as pd
from flask import Flask, jsonify, request
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from nltk.tokenize import word_tokenize
pd.options.mode.chained_assignment = None


import nltk

try:
    nltk.data.find('tokenizers/punkt')
except LookupError:
    nltk.download('punkt')
app = Flask(__name__)


#List stopwords Vietnamese

vietnamese_stopwords = [
    # Từ chung
    'các', 'và', 'là', 'muốn', 'đã', 'trong', 'khi', 'này', 'một',
    'những', 'được', 'với', 'cho', 'thì', 'tại', 'bởi', 'về', 'để',
    'nếu', 'sẽ', 'không', 'có', 'đây', 'đó', 'thấy', 'ra', 'phải',
    'ai', 'gì', 'nào', 'lại', 'hơn', 'như', 'vậy', 'chỉ', 'làm', 'lúc',

    # Từ marketing/không mang tính phân biệt trong ngữ cảnh rau củ
    'tươi', 'ngon', 'sạch', 'rẻ', 'giá', 'hàng', 'loại',
    'mua', 'bán', 'shop', 'siêu thị', 'cửa hàng', 'sản phẩm'
]


def create_connection():
    try:
        engine = create_engine(
            # Sử dụng thư viện pymysql và localhost (Cách kết nối ổn định nhất với XAMPP trên Windows)
            "mysql+pymysql://root:@localhost:3306/mealkit",
            connect_args={'connect_timeout': 5}
        )
        print("Connect to Mysql success!")
        return engine
    except Error as e:
        print(f"Error when connect to Mysql: {e}")
        return None

def close_connection(engine):
    if engine:
        engine.dispose()
        print("Connection is closed.")

def fetch_products(engine):
    try:
        query = "SELECT * FROM products WHERE stock > 0;"
        df_products = pd.read_sql(query, engine)
        print("Get products data suceess!")
        return df_products
    except Error as e:
        print(f"Error when get products: {e}")
        return pd.DataFrame()

def combine_features(row):
    features = ['name', 'description', 'price', 'unit']
    return ' '.join(
        [str(row[feature]) for feature in features if feature in row and pd.notnull(row[feature])]
    )

def preprocess_text(text):
    text = text.lower()
    tokens = word_tokenize(text)
    processed_tokens = [word for word in tokens if word not in vietnamese_stopwords and word.isalnum()]
    return ' '.join(processed_tokens)

# --- TỐI ƯU HOÁ: LƯU CACHE DỮ LIỆU ĐỂ KHÔNG LOAD LẠI MỖI REQUEST ---
cached_products_df = pd.DataFrame()
cached_tfidf_matrix = None
cached_vectorizer = None

def initialize_model():
    global cached_products_df, cached_tfidf_matrix, cached_vectorizer

    print("Bắt đầu kết nối DB")

    engine = create_connection()

    print("Đã kết nối DB")

    if engine:

        print("Bắt đầu đọc dữ liệu")

        df = fetch_products(engine)

        print("Đã đọc dữ liệu")
        print(f"Số sản phẩm: {len(df)}")

        if not df.empty:

            print("Bắt đầu xử lý dữ liệu")

            df['combineFeatures'] = df.apply(combine_features, axis=1)

            df['processedFeatures'] = df['combineFeatures'].apply(preprocess_text)

            print("Đã xử lý dữ liệu")

            print("Bắt đầu train model")

            cached_vectorizer = TfidfVectorizer()

            cached_tfidf_matrix = cached_vectorizer.fit_transform(
                df['processedFeatures']
            )

            cached_products_df = df

            print("Đã train xong")
            print("Đã tải dữ liệu và train model AI thành công!")

        close_connection(engine)

# (Đã xóa dòng khởi tạo thừa ở đây vì cuối file đã có lệnh gọi rồi)

@app.route('/api/product-recommendation', methods=['GET'])
def get_product_recommendations():
    try:
        global cached_products_df, cached_tfidf_matrix
        if cached_products_df is None or cached_products_df.empty:
            return jsonify({"error": "No products found in database"}), 404

        # Get id of product from query parameter
        product_id = request.args.get('product_id')

        # Check if product_id is valid
        if not product_id or not product_id.isdigit():
            return jsonify({"error": "Invalid or missing 'id' parameter"}), 400

        product_id = int(product_id)

        # Check if product_id exists in column 'id' of DataFrame
        product_match = cached_products_df[cached_products_df['id'] == product_id]
        if product_match.empty:
            return jsonify({"error": f"Product ID {product_id} not found or out of stock"}), 404
            
        product_index = product_match.index[0]

        # Tính độ tương đồng cho riêng sản phẩm này (Tính toán cực nhanh)
        cosine_sim = cosine_similarity(cached_tfidf_matrix[product_index], cached_tfidf_matrix)

        # Caculate point similarity for product selected
        sim_scores = list(enumerate(cosine_sim[0]))
        sim_scores_sorted = sorted(sim_scores, key=lambda x: x[1], reverse=True)[1:7]  # Get the 6 most similar products

        # Get index the 6 most similar products
        similar_products_indices = [i[0] for i in sim_scores_sorted]

        # Prepare response data
        related_products = cached_products_df.iloc[similar_products_indices]
        related_products_list = related_products['id'].tolist()

        return jsonify({"related_products": related_products_list})
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route('/api/search-products', methods=['GET'])
def search_products():
    user_input = request.args.get('keyword')

    if not user_input:
        return jsonify({'error':"Missing search query"}), 400

    try:
        global cached_products_df, cached_tfidf_matrix, cached_vectorizer
        if cached_products_df is None or cached_products_df.empty:
            return jsonify({"Error": "No products found"}), 400

        processed_input = preprocess_text(user_input)

        # Calculate TF-IDF and cosine similarity
        input_vector = cached_vectorizer.transform([processed_input])

        cosine_sim = cosine_similarity(input_vector, cached_tfidf_matrix)

        sim_scores = list(enumerate(cosine_sim[0]))
        sim_scores_sorted = sorted(sim_scores, key=lambda x: x[1], reverse=True)[:8]  # Get the 8 most similar products

        # Get index the 6 most similar products
        similar_products_indices = [i[0] for i in sim_scores_sorted]

        # Prepare response data
        related_products = cached_products_df.iloc[similar_products_indices]
        related_products_list = related_products['id'].tolist()

        return jsonify({"related_products": related_products_list})

    except Exception as e:
        return jsonify({"error": f"An error occurred: {e}"}), 500


if __name__ == '__main__':
    initialize_model()
    app.run(
        host='0.0.0.0',
        port=5555,
        debug=True,
        use_reloader=False
    )